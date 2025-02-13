import './bootstrap';
import load from './darkmode';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import collectionShow from './Images/collectionShow';

document.addEventListener('livewire:navigated', () => {
    load();
})

Livewire.on('reloadPage', () => {
    location.reload();
});

Alpine.data('collectionShow', collectionShow);

Livewire.start()
