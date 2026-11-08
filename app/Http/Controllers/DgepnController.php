<?php

namespace App\Http\Controllers;

use App\Models\ActionCle;
use App\Models\ActionMesurable;
use App\Models\AlerteEnvoyee;
use App\Models\Direction;
use App\Models\Emploi;
use App\Models\ListeValeur;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\PieceJointe;
use App\Models\ProfilAcces;
use App\Models\RoleAffectation;
use App\Models\Service;
use App\Models\SousDirection;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\TypeAgent;
use App\Support\ArchivageAgent;
use App\Support\DoublonsPersonnel;
use App\Support\HierarchieResponsables;
use App\Support\JournalFiche;
use App\Support\PonctuellesDroits;
use App\Support\RattachementAgent;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class DgepnController extends Controller
{
    /**
     * Administrateur : peut modifier tous les champs d'une fiche agent.
     */
    private function estAdmin(): bool
    {
        return auth()->user()?->profilAcces?->slug === ProfilAcces::ADMINISTRATEUR;
    }

    /**
     * Regles de validation du changement de mot de passe : uniquement par l'utilisateur lui-meme,
     * sur sa propre fiche, et seulement s'il remplit "nouveau mot de passe" (champ vide = rien ne change).
     * Retourne [] quand il n'y a pas de changement de mot de passe a traiter.
     */
    private function reglesMotDePasse(Request $request, $personnelId): array
    {
        if (! $request->filled('password') || ! $this->estLaFicheDeLUtilisateur($personnelId)) {
            return [];
        }

        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    private function messagesMotDePasse(): array
    {
        return [
            'current_password.required' => 'Saisissez votre mot de passe actuel pour en changer.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.',
            'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée par un autre agent.',
            'telephone.regex' => 'Numéro de téléphone invalide (chiffres, espaces, +, tirets ; 8 à 20 caractères).',
            'telephone_2.regex' => 'Numéro de téléphone 2 invalide (chiffres, espaces, +, tirets ; 8 à 20 caractères).',
            'date_naissance.before_or_equal' => "Date de naissance invraisemblable : l'agent doit avoir au moins 16 ans.",
            'date_naissance.after_or_equal' => 'Date de naissance invraisemblable : au plus 90 ans.',
        ];
    }

    /**
     * L'utilisateur connecte est-il l'agent de cette fiche ? (lien : users.personnel_id)
     */
    private function estLaFicheDeLUtilisateur($personnelId): bool
    {
        $moi = auth()->user()?->personnel_id;

        return $moi && (int) $moi === (int) $personnelId;
    }

    /*
    |--------------------------------------------------------------------------
    | Direction
    |--------------------------------------------------------------------------
    */

    public function directions()
    {
        $directions = Direction::orderBy('nom')->get();

        return view('dgepn.direction', compact('directions'));
    }

    public function storeDirection(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'direction.modifier' : 'direction.creer');

        $validator = Validator::make($request->all(), [
            'code' => [
                'nullable', 'string', 'max:50',
                $id ? "unique:directions,code,{$id}" : 'unique:directions,code',
            ],
            'nom' => [
                'required', 'string', 'max:255',
                $id ? "unique:directions,nom,{$id}" : 'unique:directions,nom',
            ],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $direction = Direction::findOrFail($id);
            $direction->update($donnees);
            $message = 'Direction mise à jour avec succès !';
        } else {
            Direction::create($donnees);
            $message = 'Direction créée avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getDirection($id)
    {
        $direction = Direction::findOrFail($id);

        return response()->json($direction);
    }

    public function deleteDirection($id)
    {
        Gate::authorize('direction.supprimer');
        $direction = Direction::find($id);

        if (! $direction) {
            return response()->json(['status' => false, 'message' => 'Direction introuvable.'], 404);
        }

        $direction->delete();

        return response()->json(['status' => true, 'message' => 'Direction supprimée avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Sous-direction
    |--------------------------------------------------------------------------
    */

    public function sousDirections()
    {
        $sousDirections = SousDirection::with('direction')->orderBy('nom')->get();
        $directions = Direction::orderBy('nom')->get();

        return view('dgepn.sous_direction', compact('sousDirections', 'directions'));
    }

    public function storeSousDirection(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'sous_direction.modifier' : 'sous_direction.creer');

        $validator = Validator::make($request->all(), [
            'direction_id' => ['required', 'exists:directions,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        if ($id) {
            $sousDirection = SousDirection::findOrFail($id);
            $sousDirection->update($donnees);
            $message = 'Sous-direction mise à jour avec succès !';
        } else {
            SousDirection::create($donnees);
            $message = 'Sous-direction créée avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getSousDirection($id)
    {
        $sousDirection = SousDirection::findOrFail($id);

        return response()->json($sousDirection);
    }

    public function deleteSousDirection($id)
    {
        Gate::authorize('sous_direction.supprimer');
        $sousDirection = SousDirection::find($id);

        if (! $sousDirection) {
            return response()->json(['status' => false, 'message' => 'Sous-direction introuvable.'], 404);
        }

        $sousDirection->delete();

        return response()->json(['status' => true, 'message' => 'Sous-direction supprimée avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Service (rattache a une sous-direction OU directement a une direction)
    |--------------------------------------------------------------------------
    */

    public function services()
    {
        $services = Service::with(['sousDirection.direction', 'directionDirecte'])->orderBy('nom')->get();
        $directions = Direction::orderBy('nom')->get();
        $sousDirections = SousDirection::with('direction')->orderBy('nom')->get();

        return view('dgepn.service', compact('services', 'directions', 'sousDirections'));
    }

    public function storeService(Request $request)
    {
        $id = $request->input('id_');
        Gate::authorize($id ? 'service.modifier' : 'service.creer');

        $validator = Validator::make($request->all(), [
            'rattachement' => ['required', 'in:sous_direction,direction,dg'],
            'sous_direction_id' => ['required_if:rattachement,sous_direction', 'nullable', 'exists:sous_directions,id'],
            'direction_id' => ['required_if:rattachement,direction', 'nullable', 'exists:directions,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();

        // On ne garde que le champ correspondant au type de rattachement choisi ;
        // "dg" : ni sous-direction ni direction (rattache directement a la DG).
        if ($donnees['rattachement'] === 'sous_direction') {
            $donnees['direction_id'] = null;
        } elseif ($donnees['rattachement'] === 'direction') {
            $donnees['sous_direction_id'] = null;
        } else {
            $donnees['sous_direction_id'] = null;
            $donnees['direction_id'] = null;
        }
        unset($donnees['rattachement']);

        if ($id) {
            $service = Service::findOrFail($id);
            $service->update($donnees);
            $message = 'Service mis à jour avec succès !';
        } else {
            Service::create($donnees);
            $message = 'Service créé avec succès !';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getService($id)
    {
        $service = Service::findOrFail($id);

        $rattachement = $service->sous_direction_id
            ? 'sous_direction'
            : ($service->direction_id ? 'direction' : 'dg');

        return response()->json([
            'id' => $service->id,
            'code' => $service->code,
            'nom' => $service->nom,
            'description' => $service->description,
            'rattachement' => $rattachement,
            'sous_direction_id' => $service->sous_direction_id,
            'direction_id' => $service->direction_id,
        ]);
    }

    public function deleteService($id)
    {
        Gate::authorize('service.supprimer');
        $service = Service::find($id);

        if (! $service) {
            return response()->json(['status' => false, 'message' => 'Service introuvable.'], 404);
        }

        $service->delete();

        return response()->json(['status' => true, 'message' => 'Service supprimé avec succès.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Personnel
    |--------------------------------------------------------------------------
    */

    public function personnels()
    {
        $personnels = Personnel::with([
            'service', 'typeAgent', 'emploi', 'user',
            'mandats' => fn ($q) => $q->actifA()->with(['direction', 'sousDirection', 'service']),
        ])->nonArchives()->orderBy('nom')->get();
        $services = Service::orderBy('nom')->get();
        $typesAgent = TypeAgent::where('actif', true)->orderBy('libelle')->get();
        $emplois = Emploi::with('familleEmploi')->where('actif', true)->orderBy('libelle')->get();

        return view('dgepn.personnel', compact('personnels', 'services', 'typesAgent', 'emplois'));
    }

    public function storePersonnel(Request $request)
    {
        $id = $request->input('id_');

        // CREATION d'un agent : administrateur uniquement
        abort_if(! $id && ! $this->estAdmin(), 403, "Seul l'administrateur peut créer un agent.");

        // Changement de mot de passe (agent connecte sur sa propre fiche uniquement) ; [] = rien a faire
        $reglesMdp = $this->reglesMotDePasse($request, $id);
        $champsMdp = ['current_password', 'password', 'password_confirmation'];

        // MODIFICATION d'une fiche existante :
        //  - administrateur : tous les champs (traitement ci-dessous) ;
        //  - l'agent concerne : uniquement email, telephone, lieu d'habitation, photo (+ son mot de passe) ;
        //  - tout autre utilisateur : refuse.
        if ($id && ! $this->estAdmin()) {
            abort_unless($this->estLaFicheDeLUtilisateur($id), 403, "Vous n'êtes pas autorisé à modifier cette fiche.");

            $validator = Validator::make($request->all(), array_merge([
                'nom' => ['required', 'string', 'max:255'],
                'prenom' => ['nullable', 'string', 'max:255'],
                'date_naissance' => ['nullable', 'date', 'before_or_equal:-16 years', 'after_or_equal:-90 years'],
                'lieu_naissance' => ['nullable', 'string', 'max:255'],
                'civilite_id' => ['nullable', Rule::exists('listes_valeurs', 'id')->where('liste', 'civilite')],
                'genre_id' => ['nullable', Rule::exists('listes_valeurs', 'id')->where('liste', 'genre')],
                'statut_matrimonial_id' => ['nullable', Rule::exists('listes_valeurs', 'id')->where('liste', 'statut_matrimonial')],
                'nombre_enfants' => ['nullable', 'integer', 'min:0', 'max:30'],
                'nom_pere' => ['nullable', 'string', 'max:255'],
                'nom_mere' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255', Rule::unique('personnel', 'email')->ignore($id)],
                'telephone' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9 ().\-]{8,20}$/'],
                'lieu_habitation' => ['nullable', 'string', 'max:255'],
                'telephone_2' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9 ().\-]{8,20}$/'],
            ], $this->reglesPhoto(), $reglesMdp), array_merge($this->messagesPhoto(), $this->messagesMotDePasse()));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->except($champsMdp));
            }

            // validated() ne renvoie QUE les champs ayant une regle : le reste de la requete est ignore.
            $donnees = $validator->validated();
            unset($donnees['photo'], $donnees['supprimer_photo'], $donnees['current_password'], $donnees['password']);

            $personnel = Personnel::findOrFail($id);
            $avant = JournalFiche::avant($personnel);
            $personnel->update($donnees);
            JournalFiche::modification($personnel, $avant);
            $this->enregistrerPhoto($personnel, $request);

            $message = 'Vos informations ont été mises à jour avec succès !';
            if ($reglesMdp) {
                auth()->user()->update(['password' => Hash::make($request->input('password'))]);
                $message .= ' Votre mot de passe a été modifié.';
            }

            return redirect()->back()->with('success', $message);
        }

        $validator = Validator::make($request->all(), array_merge([
            'type_agent_id' => ['nullable', 'exists:types_agent,id'],
            'emploi_id' => ['nullable', 'exists:emplois,id'],
            'matricule' => [
                'nullable', 'string', 'max:50',
                $id ? "unique:personnel,matricule,{$id}" : 'unique:personnel,matricule',
            ],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date', 'before_or_equal:-16 years', 'after_or_equal:-90 years'],
            'lieu_naissance' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('personnel', 'email')->ignore($id)],
            'telephone' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9 ().\-]{8,20}$/'],
            'lieu_habitation' => ['nullable', 'string', 'max:255'],
            'actif' => ['required', 'boolean'],
            'civilite_id' => ['nullable', Rule::exists('listes_valeurs', 'id')->where('liste', 'civilite')],
            'genre_id' => ['nullable', Rule::exists('listes_valeurs', 'id')->where('liste', 'genre')],
            'statut_matrimonial_id' => ['nullable', Rule::exists('listes_valeurs', 'id')->where('liste', 'statut_matrimonial')],
            'nombre_enfants' => ['nullable', 'integer', 'min:0', 'max:30'],
            'nom_pere' => ['nullable', 'string', 'max:255'],
            'nom_mere' => ['nullable', 'string', 'max:255'],
            'telephone_2' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9 ().\-]{8,20}$/'],
        ], $this->reglesPhoto(), $reglesMdp), array_merge($this->messagesPhoto(), $this->messagesMotDePasse()));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->except($champsMdp));
        }

        $donnees = $validator->validated();

        // Doublon probable (même e-mail, même téléphone, même nom et prénom) : refusé avec l'agent concerné indiqué
        $verifier = ! $id;
        if ($id) {
            $courant = Personnel::find($id);
            foreach (['nom', 'prenom', 'email', 'telephone'] as $champ) {
                $verifier = $verifier || (string) ($donnees[$champ] ?? '') !== (string) $courant?->{$champ};
            }
            $verifier = $verifier || (string) ($donnees['date_naissance'] ?? '') !== (string) $courant?->date_naissance?->toDateString();
        }
        $doublon = $verifier ? DoublonsPersonnel::trouver($donnees, $id ? (int) $id : null) : null;
        if ($doublon) {
            $a = $doublon['agent'];

            return redirect()->back()->withErrors(['nom' => "Doublon probable : {$doublon['raison']} que {$a->prenom} {$a->nom} (matricule {$a->matricule}). Si ce sont bien deux personnes, distinguez-les (date de naissance, téléphone différent)."])->withInput($request->except($champsMdp));
        }

        // La photo est geree a part (fichier), le mot de passe aussi : pas des colonnes ordinaires de "personnel".
        unset($donnees['photo'], $donnees['supprimer_photo'], $donnees['current_password'], $donnees['password']);

        if ($id) {
            $personnel = Personnel::findOrFail($id);
            $avant = JournalFiche::avant($personnel);
            $personnel->update($donnees);
            JournalFiche::modification($personnel, $avant);
            $message = 'Agent mis à jour avec succès !';
        } else {
            $personnel = Personnel::create(array_merge($donnees, ['fonction' => 'Agent']));
            JournalFiche::creation($personnel);
            $message = 'Agent créé avec succès !';
        }

        $this->enregistrerPhoto($personnel, $request);

        // Un administrateur qui modifie SA PROPRE fiche peut aussi changer son mot de passe
        if ($reglesMdp) {
            auth()->user()->update(['password' => Hash::make($request->input('password'))]);
            $message .= ' Votre mot de passe a été modifié.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getPersonnel($id)
    {
        // Donnees de la fiche (telephone, email...) : administrateur ou agent concerne seulement
        abort_unless($this->estAdmin() || $this->estLaFicheDeLUtilisateur($id), 403, 'Accès refusé.');

        // photo_url est ajoute automatiquement au JSON (voir $appends du modele)
        $personnel = Personnel::findOrFail($id);

        return response()->json($personnel);
    }

    /**
     * « Supprimer » un agent = l'ARCHIVER : aucune fiche n'est jamais supprimée. Son poste prend fin, son compte est
     * désactivé, tout son historique reste consultable (Personnel > Archives). Administrateur uniquement.
     */
    public function deletePersonnel(Request $request, $id)
    {
        if (! $this->estAdmin()) {
            return response()->json(['status' => false, 'message' => "Seul l'administrateur peut archiver un agent."], 403);
        }
        $personnel = Personnel::find($id);
        if (! $personnel) {
            return response()->json(['status' => false, 'message' => 'Agent introuvable.'], 404);
        }

        try {
            $r = ArchivageAgent::archiver($personnel, trim((string) $request->input('motif')) ?: 'Archivage depuis la liste du personnel', $request->user());
        } catch (DomainException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => true, 'message' => "Agent archivé (aucune fiche n'est supprimée) : {$r['mandats']} poste(s) terminé(s), {$r['comptes']} compte(s) désactivé(s)".($r['taches'] ? ", {$r['taches']} tâche(s) ouverte(s) à réaffecter." : '.')]);
    }

    /**
     * Changement (ou retrait) de la photo depuis la fiche de l'agent.
     */
    public function updatePhotoPersonnel(Request $request, $id)
    {
        abort_unless($this->estAdmin() || $this->estLaFicheDeLUtilisateur($id), 403, "Vous n'êtes pas autorisé à modifier cette photo.");

        $personnel = Personnel::findOrFail($id);

        $validator = Validator::make($request->all(), $this->reglesPhoto(), $this->messagesPhoto());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->enregistrerPhoto($personnel, $request);

        return redirect()->back()->with('success', 'Photo mise à jour avec succès !');
    }

    /**
     * Fiche complete d'un agent : identite, rattachement, taches, activite, alertes recues, compte.
     */
    public function fichePersonnel($id)
    {
        $personnel = Personnel::with([
            'service.sousDirection.direction',
            'service.directionDirecte',
            'typeAgent',
            'emploi.familleEmploi',
            'user.profilAcces',
        ])->findOrFail($id);

        // Direction / service affichés d'après le poste occupé quand l'agent n'a pas de service (affichage seulement)
        $rattachement = RattachementAgent::appliquer($personnel);

        // Taches ou l'agent est affecte, quel que soit son role (echeance proche en premier)
        $taches = Tache::with(['statutExecution', 'actionMesurable.actionCle', 'affectations'])
            ->whereHas('affectations', fn ($a) => $a->where('personnel_id', $personnel->id))
            ->orderByRaw('date_fin is null, date_fin asc')
            ->get();

        $rolesParId = RoleAffectation::pluck('libelle', 'id');

        $compteurs = [
            'total' => $taches->count(),
            'en_cours' => $taches->filter(fn ($t) => $t->statutExecution?->code === StatutExecution::EN_COURS)->count(),
            'en_retard' => $taches->filter(fn ($t) => $t->estEnRetard())->count(),
            'terminees' => $taches->filter(fn ($t) => $t->statutExecution?->code === StatutExecution::TERMINEE)->count(),
        ];

        $nbActionsCles = ActionCle::whereHas('affectations', fn ($a) => $a->where('personnel_id', $personnel->id))->count();
        $nbActionsMesurables = ActionMesurable::whereHas('affectations', fn ($a) => $a->where('personnel_id', $personnel->id))->count();

        // Activite recente : ses taches mises a jour, la plus recente d'abord.
        // (Le detail "qui a change quoi" viendra avec l'historique des modifications.)
        $activite = $taches->filter(fn ($t) => $t->derniere_mise_a_jour_le)
            ->sortByDesc('derniere_mise_a_jour_le')
            ->take(15)
            ->values();

        $alertes = AlerteEnvoyee::with(['tache', 'regleAlerte', 'canalNotification'])
            ->where('personnel_id', $personnel->id)
            ->orderByDesc('envoye_le')
            ->limit(30)
            ->get();

        // Droits de modification : administrateur (tous les champs) ou agent concerne (email, telephone, habitation)
        $estAdmin = $this->estAdmin();
        $peutModifier = $estAdmin || $this->estLaFicheDeLUtilisateur($personnel->id);
        $peutChangerMdp = $this->estLaFicheDeLUtilisateur($personnel->id); // son propre mot de passe uniquement

        // Données personnelles (naissance, famille, contacts, matricule, compte) : l'agent, l'administrateur, le DG et sa chaîne hiérarchique seulement
        $utilisateur = auth()->user();
        $voitPrive = $estAdmin || $this->estLaFicheDeLUtilisateur($personnel->id) || $utilisateur->hasRole('DG')
            || ($utilisateur->personnel_id && in_array((int) $utilisateur->personnel_id, PonctuellesDroits::chaineIds($personnel), true));

        // Hierarchie : superieur direct (chef de service, puis sous-direction, direction, DG) et unites qu'il dirige aujourd'hui
        $superieur = HierarchieResponsables::superieurDirect($personnel);
        $unitesDirigees = $personnel->mandats()->actifA()->with(['direction', 'sousDirection', 'service'])->get()
            ->map(fn ($m) => $m->libelleUnite().($m->type === MandatResponsable::INTERIM ? ' (intérim)' : ''))
            ->implode(' · ');

        // Listes du formulaire (aussi necessaires en lecture seule : elles affichent la valeur actuelle)
        $services = collect();
        $typesAgent = collect();
        $emplois = collect();

        if ($peutModifier) {
            $services = Service::orderBy('nom')->get();
            $typesAgent = TypeAgent::where(fn ($q) => $q->where('actif', true)->orWhere('id', $personnel->type_agent_id))
                ->orderBy('libelle')->get();
            $emplois = Emploi::with('familleEmploi')
                ->where(fn ($q) => $q->where('actif', true)->orWhere('id', $personnel->emploi_id))
                ->orderBy('libelle')->get();
        }

        $categoriesDejaDeposees = PieceJointe::withTrashed()
            ->where('attachable_type', $personnel->getMorphClass())
            ->where('attachable_id', $personnel->id)
            ->where('categorie', 'like', 'dossier_%')
            ->pluck('categorie')
            ->map(fn ($categorie) => Str::after($categorie, 'dossier_'))
            ->all();
        $typesDossierDisponibles = ListeValeur::deListe('type_document')
            ->whereNotIn('code', $categoriesDejaDeposees)
            ->orderBy('libelle')
            ->get(['code', 'libelle']);
        $peutAjouterDossier = $this->estAdmin() || $this->estLaFicheDeLUtilisateur($personnel->id);

        return view('dgepn.personnel_fiche', compact(
            'personnel', 'taches', 'rolesParId', 'compteurs', 'nbActionsCles', 'nbActionsMesurables', 'activite', 'alertes',
            'estAdmin', 'voitPrive', 'peutModifier', 'peutChangerMdp', 'peutAjouterDossier', 'typesDossierDisponibles', 'superieur', 'unitesDirigees', 'services', 'typesAgent', 'emplois', 'rattachement'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Photos des agents (fichiers dans public/uploads/personnel)
    |--------------------------------------------------------------------------
    */

    private function reglesPhoto(): array
    {
        return [
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'supprimer_photo' => ['nullable', 'boolean'],
        ];
    }

    private function messagesPhoto(): array
    {
        return [
            'photo.image' => 'Le fichier choisi doit être une image.',
            'photo.mimes' => 'La photo doit être au format JPG, PNG ou WebP.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'photo.uploaded' => 'Le téléversement de la photo a échoué : le fichier dépasse peut-être la taille autorisée par le serveur.',
        ];
    }

    private function enregistrerPhoto(Personnel $personnel, Request $request): void
    {
        if ($request->hasFile('photo')) {
            $this->supprimerFichierPhoto($personnel);

            $dossier = public_path('uploads/personnel');
            if (! is_dir($dossier)) {
                mkdir($dossier, 0755, true);
            }

            $fichier = $request->file('photo');
            $nom = Str::uuid().'.'.$fichier->extension();
            $fichier->move($dossier, $nom);

            $personnel->update(['photo' => $nom]);
        } elseif ($request->boolean('supprimer_photo')) {
            $this->supprimerFichierPhoto($personnel);
            $personnel->update(['photo' => null]);
        }
    }

    private function supprimerFichierPhoto(Personnel $personnel): void
    {
        if ($personnel->photo) {
            $chemin = public_path('uploads/personnel/'.$personnel->photo);

            if (is_file($chemin)) {
                @unlink($chemin);
            }
        }
    }
}
