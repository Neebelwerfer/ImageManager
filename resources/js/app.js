import './bootstrap';
import load from './darkmode';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import collectionShow from './Images/collectionShow';
import MultiSelect from './Images/MultiSelect';
import Upload from './Images/Upload';

document.addEventListener('livewire:navigated', () => {
    load();
})

Livewire.on('reloadPage', () => {
    location.reload();
});


document.addEventListener('livewire:init', () => {
    Alpine.data('collectionShow', collectionShow);
    Alpine.data('multiSelect', MultiSelect);
    Alpine.data('upload', Upload)
});

Livewire.start()
