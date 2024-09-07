import './bootstrap';

import load from './darkmode';

document.addEventListener('livewire:navigated', () => {
    load();
})
