<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Http\Request;

/**
 * Notifications de l'utilisateur connecte (table `notifications`, canal "database").
 * Chaque utilisateur ne voit et ne modifie que les siennes.
 */
class NotificationController extends Controller
{
    /** Page "Mes notifications" : ?filtre=non_lues pour ne garder que les non lues (300 dernieres au maximum). */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filtre = $request->query('filtre') === 'non_lues' ? 'non_lues' : 'toutes';

        $notifications = ($filtre === 'non_lues' ? $user->unreadNotifications() : $user->notifications())
            ->latest()->limit(300)->get();

        return view('notifications.index', [
            'notifications' => $notifications,
            'filtre' => $filtre,
            'nbNonLues' => $user->unreadNotifications()->count(),
        ]);
    }

    /** Marque la notification comme lue puis ouvre la tache concernee (lien du menu deroulant et de la page). */
    public function ouvrir(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Notifications qui indiquent leur propre destination (demandes de justification, décisions)
        if (! empty($notification->data['lien'])) {
            return redirect($notification->data['lien']);
        }

        $tacheId = $notification->data['tache_id'] ?? null;

        return $tacheId && Tache::whereKey($tacheId)->exists()
            ? redirect()->route('feuille_de_route.tache.details', $tacheId)
            : redirect()->route('notifications.index');
    }

    public function toutLire()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
