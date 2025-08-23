import './bootstrap';
import { createApp } from 'vue';
import StudySession from './components/StudySession.vue';

const el = document.getElementById('app');

//Only mount the Vue app IF the element was found
if (el) {
    const app = createApp({});

    app.component('study-session', StudySession);

    app.mount('#app');
}