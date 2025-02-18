import './bootstrap';
import load from './darkmode';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import collectionShow from './Images/collectionShow';
import MultiSelect from './Images/MultiSelect';

document.addEventListener('livewire:navigated', () => {
    load();
})

Livewire.on('reloadPage', () => {
    location.reload();
});


document.addEventListener('livewire:init', () => {
    Alpine.data('collectionShow', collectionShow);
    Alpine.data('multiSelect', MultiSelect);
});

Livewire.start()
