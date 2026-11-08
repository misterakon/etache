<!-- latest jquery-->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<!-- feather icon js-->
<script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
<!-- scrollbar js-->
<script src="{{ asset('assets/js/scrollbar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
<!-- Sidebar jquery-->
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/modalpage/custom-modal.js') }}"> </script>
<!-- Plugins JS start-->
<script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('assets/js/sidebar-pin.js') }}"></script>
<script src="{{ asset('assets/js/menu-actif.js') }}?v={{ filemtime(public_path('assets/js/menu-actif.js')) }}"></script>
<script src="{{ asset('assets/js/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/slick/slick.js') }}"></script>
<script src="{{ asset('assets/js/header-slick.js') }}"></script>

<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
<script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
<script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alert/app.js') }}"></script>
<script src="{{ asset('assets/js/height-equal.js') }}"></script>

<!-- tooltip JS Ends-->
<script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{ asset('assets/js/script1.js') }}"></script>
<script src="{{ asset('assets/js/theme-customizer/customizer.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>


{{-- Verrouillage de l'écran après inactivité (délais : Réglages > Sécurité de la session) --}}
@auth
  @if (\App\Support\Reglages::get('securite.verrouillage_actif'))
    <div id="verrou-config" hidden
         data-minutes="{{ (int) \App\Support\Reglages::get('securite.inactivite_minutes') }}"
         data-avertissement="{{ (int) \App\Support\Reglages::get('securite.avertissement_secondes') }}"
         data-url-verrou="{{ route('verrouillage.lock') }}"
         data-url-ping="{{ route('session.ping') }}"
         data-url-login="{{ route('login') }}"
         data-jeton="{{ csrf_token() }}"></div>
    <script src="{{ asset('assets/js/verrouillage.js') }}?v={{ filemtime(public_path('assets/js/verrouillage.js')) }}"></script>
  @endif
@endauth

{{-- Pour la gestion des modal --}}
<script>
    $.fn.modal = function (action) {
        return this.each(function () {
            const $el = $(this);

            if (action === 'hide') {
                document.activeElement?.blur();
                $el.removeClass('show').css('display', 'none');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            } else {
                $el.addClass('show').css('display', 'block');
                $('body').addClass('modal-open');
                if (!$('.modal-backdrop').length) {
                    $('body').append('<div class="modal-backdrop fade show" style="opacity: 0.35;"></div>');
                }
            }
        });
    };

    $(document).on('click', '[data-bs-dismiss="modal"]', function () {
        $(this).closest('.modal').modal('hide');
    });
</script>