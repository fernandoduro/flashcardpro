import './bootstrap';
import { createApp } from 'vue';
import StudySession from './components/StudySession.vue';

const app = createApp({});
app.component('study-session', StudySession);
app.mount('#app');