<template>
    <div class="max-w-2xl mx-auto">

        <!-- Error State -->
        <div v-if="error" class="text-center p-10 bg-white rounded-lg shadow-md border-2 border-red-200">
            <i class="fa-solid fa-circle-exclamation text-4xl text-red-500"></i>
            <p class="mt-4 text-lg font-semibold text-gray-700">Could not start the study session.</p>
            <p class="mt-1 text-sm text-gray-500">{{ error }}</p>
            <a :href="deckUrl" class="mt-6 inline-flex items-center px-6 py-3 bg-gray-700 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-gray-600">
                Back to Deck
            </a>
        </div>

        <!-- Loading State -->
        <div v-else-if="loading" class="text-center p-10 bg-white rounded-lg shadow-md">
            <i class="fa-solid fa-spinner fa-spin text-4xl text-primary-500"></i>
            <p class="mt-4 text-lg text-gray-600">Preparing your study session...</p>
        </div>

        <!-- Session Finished State -->
        <div v-else-if="sessionFinished" class="text-center p-10 bg-white rounded-lg shadow-md">
            <i class="fa-solid fa-circle-check text-6xl text-green-500"></i>
            <h2 class="mt-4 text-3xl font-bold text-gray-800">Session Complete!</h2>
            <p class="mt-2 text-lg text-gray-600">You got <span class="font-bold text-primary-600">{{ correctAnswers }}</span> out of <span class="font-bold">{{ cards.length }}</span> correct.</p>
            <a :href="deckUrl" class="mt-6 inline-flex items-center px-6 py-3 bg-gray-700 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-gray-600">
                Back to Deck
            </a>
        </div>

        <!-- Main Study View -->
        <div v-else-if="currentCard" class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header with Progress Bar -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ deck.name }}</h2>
                    <span class="text-sm font-semibold text-gray-500">{{ currentCardIndex + 1 }} / {{ cards.length }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-300 ease-out" :style="{ width: progressPercentage + '%' }"></div>
                </div>
            </div>

            <!-- Flashcard with Transition -->
            <div class="p-8 sm:p-12 min-h-[20rem] flex flex-col justify-center items-center text-center">
                <Transition name="fade" mode="out-in">
                    <div :key="currentCard.id + (answerVisible ? '-ans' : '-q')" class="w-full">
                        <!-- Question View -->
                        <div v-if="!answerVisible">
                            <p class="text-2xl sm:text-3xl text-gray-700">{{ currentCard.question }}</p>
                            <button @click="revealAnswer" type="button" class="mt-8 inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-primary-500">
                                Reveal Answer
                            </button>
                        </div>

                        <!-- Answer View -->
                        <div v-else>
                            <p class="text-xl text-gray-500 mb-2">{{ currentCard.question }}</p>
                            <p class="text-3xl sm:text-4xl font-bold text-green-600 mb-8">{{ currentCard.answer }}</p>
                            <div class="flex flex-col sm:flex-row justify-center gap-4">
                                <button @click="recordResult(true)" type="button" class="inline-flex items-center justify-center px-6 py-3 bg-green-500 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-600">
                                    <i class="fa-solid fa-check mr-2"></i> I Got It Right
                                </button>
                                <button @click="recordResult(false)" type="button" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <i class="fa-solid fa-xmark mr-2"></i> Maybe Next Time
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    deck: Object,
});

const deckUrl = `/decks/${props.deck.id}`;

// State
const loading = ref(true);
const error = ref(null);
const cards = ref([]);
const currentCardIndex = ref(0);
const answerVisible = ref(false);
const sessionFinished = ref(false);
const correctAnswers = ref(0);
const studyId = ref(null);

// Computed Properties
const currentCard = computed(() => cards.value[currentCardIndex.value]);
const progressPercentage = computed(() => {
    // Guard against division by zero if a deck has no cards
    if (!cards.value || cards.value.length === 0) {
        return 0;
    }
    return ((currentCardIndex.value + 1) / cards.value.length) * 100;
});

// Create a dedicated API client that includes the auth token
const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
        'Accept': 'application/json',
    }
});

// Lifecycle Hook
onMounted(async () => {
    // Pre-flight check for the API token
    if (!localStorage.getItem('api_token')) {
        error.value = "API token not found in local storage. Please log in or generate a token.";
        loading.value = false;
        return;
    }

    try {
        const sessionResponse = await apiClient.post('/studies', { deck_id: props.deck.id });
        studyId.value = sessionResponse.data.study_id;

        const cardsResponse = await apiClient.get(`/decks/${props.deck.id}/cards`);
        cards.value = cardsResponse.data.data;

        if (cards.value.length === 0) {
            sessionFinished.value = true;
        }
    } catch (err) {
        console.error("Failed to start study session:", err);
        // Provide a user-friendly error from the API response if available
        error.value = err.response?.data?.message || "An unexpected API error occurred.";
    } finally {
        loading.value = false;
    }
});

// Methods
const revealAnswer = () => {
    answerVisible.value = true;
};

const recordResult = async (isCorrect) => {
    if (isCorrect) {
        correctAnswers.value++;
    }
    try {
        await apiClient.post('/study-results', {
            study_id: studyId.value,
            card_id: currentCard.value.id,
            is_correct: isCorrect
        });
        nextCard();
    } catch (err) {
        console.error("Failed to record result:", err);
        error.value = "Could not save your answer. Please check your connection and try again.";
    }
};

const nextCard = () => {
    answerVisible.value = false;
    if (currentCardIndex.value < cards.value.length - 1) {
        currentCardIndex.value++;
    } else {
        finishSession();
    }
};

const finishSession = async () => {
    try {
        await apiClient.patch(`/studies/${studyId.value}/complete`);
        sessionFinished.value = true;
    } catch (err) {
        console.error("Failed to complete session:", err);
        // This is a non-critical error, so we can still show the finished screen
        sessionFinished.value = true;
    }
};
</script>

<style scoped>
/* Smooth fade transition for the flashcard content */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>