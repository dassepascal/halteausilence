<div wire:ignore>
    <div id="calendar" style="min-height:400px; background:#fff;"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', () => {
        console.log("✅ Calendrier Livewire prêt, chargement des events…");

        Livewire.emit('loadEvents');

        Livewire.on('refreshCalendar', (events) => {
            console.log("📅 Events reçus via Livewire :", events);

            if (typeof window.initAdminCalendar === 'function') {
                window.initAdminCalendar({
                    selector: '#calendar',
                    locale: 'fr',
                    events: events
                });
            } else {
                console.error("❌ initAdminCalendar non disponible");
            }
        });
    });
</script>
@endpush
