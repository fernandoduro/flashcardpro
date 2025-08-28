import './bootstrap';
import { createApp } from 'vue';
import StudySession from './components/StudySession.vue';

const el = document.getElementById('app');

//Only mount the Vue app IF the element was found
if (el) {
    const app = createApp({});

    app.component('study-session', StudySession);

    app.mount('#app');

    // Add Livewire navigation event listener to reinitialize Vue if needed
    document.addEventListener('livewire:navigated', () => {
        // Re-mount Vue app after Livewire navigation if element still exists
        const newEl = document.getElementById('app');
        if (newEl && !newEl._v_app) {
            const newApp = createApp({});
            newApp.component('study-session', StudySession);
            newApp.mount('#app');
        }
    });
}