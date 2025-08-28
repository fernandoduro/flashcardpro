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
            <p v-if="requestedCardCount && requestedCardCount.value < deck.cards_count" class="text-sm text-gray-500 mt-2">
                You studied {{ requestedCardCount.value }} cards from this deck of {{ deck.cards_count }} total cards.
            </p>
            <a :href="deckUrl" class="mt-6 inline-flex items-center px-6 py-3 bg-gray-700 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-gray-600">
                Back to Deck
            </a>
        </div>

        <!-- Main Study View -->
        <div v-else-if="currentCard" class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header with Progress Bar -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ deck.name }}</h2>
                        <p v-if="requestedCardCount && requestedCardCount.value < deck.cards_count" class="text-xs text-gray-500 mt-1">
                            Studying {{ requestedCardCount.value }} of {{ deck.cards_count }} cards
                        </p>
                    </div>
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
                            <button
                                @click="revealAnswer"
                                :disabled="isRevealingAnswer"
                                type="button"
                                class="cursor-pointer mt-8 inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                            >
                                <i v-if="isRevealingAnswer" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                <span v-if="isRevealingAnswer">Revealing...</span>
                                <span v-else>Reveal Answer</span>
                            </button>
                        </div>

                        <!-- Answer View -->
                        <div v-else>
                            <p class="text-xl text-gray-500 mb-2">{{ currentCard.question }}</p>
                            <p class="text-3xl sm:text-4xl font-bold text-green-600 mb-8">{{ currentCard.answer }}</p>
                            <div class="flex flex-col sm:flex-row justify-center gap-4">
                                <button
                                    @click="recordResult(true)"
                                    :disabled="isSubmittingResult"
                                    type="button"
                                    class="cursor-pointer inline-flex items-center justify-center px-6 py-3 bg-green-500 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                                >
                                    <i v-if="isSubmittingResult" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fa-solid fa-check mr-2"></i>
                                    <span v-if="isSubmittingResult">Submitting...</span>
                                    <span v-else>I Got It Right</span>
                                </button>
                                <button
                                    @click="recordResult(false)"
                                    :disabled="isSubmittingResult"
                                    type="button"
                                    class="cursor-pointer inline-flex items-center justify-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                                >
                                    <i v-if="isSubmittingResult" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fa-solid fa-xmark mr-2"></i>
                                    <span v-if="isSubmittingResult">Submitting...</span>
                                    <span v-else>Maybe Next Time</span>
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
    requestedCardCount: {
        type: Number,
        default: null,
    },
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
const isSubmittingResult = ref(false);
const isRevealingAnswer = ref(false);

// Computed Properties
const currentCard = computed(() => cards.value[currentCardIndex.value]);
const progressPercentage = computed(() => {
    // Guard against division by zero if a deck has no cards
    if (!cards.value || cards.value.length === 0) {
        return 0;
    }
    return ((currentCardIndex.value + 1) / cards.value.length) * 100;
});

// Ensure requestedCardCount is always a number (handles string conversion)
const requestedCardCount = computed(() => {
    const count = props.requestedCardCount;
    return count !== null ? parseInt(count, 10) : null;
});

// Helper function to safely get API token
const getApiToken = () => {
    try {
        if (typeof Storage === 'undefined') {
            throw new Error('LocalStorage is not available in this environment');
        }
        const token = localStorage.getItem('api_token');
        if (!token) {
            throw new Error('API token not found in localStorage');
        }
        return token;
    } catch (e) {
        console.error('Error accessing API token:', e.message);
        throw new Error('Unable to retrieve API token. Please log in again.');
    }
};

// Create a dedicated API client that includes the auth token
const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Accept': 'application/json',
    },
    timeout: 30000, // 30 second timeout
});

// Add request interceptor to dynamically set auth token
apiClient.interceptors.request.use(
    (config) => {
        config.headers.Authorization = `Bearer ${getApiToken()}`;
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add response interceptor for better error handling
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        // Enhanced error handling with retry logic for transient failures
        if (error.code === 'ECONNABORTED' || error.response?.status >= 500) {
            console.warn('API request failed, might be transient:', error.message);
        }
        return Promise.reject(error);
    }
);

onMounted(async () => {
    try {
        getApiToken();
    } catch (e) {
        error.value = e.message;
        loading.value = false;
        return;
    }

    try {
        const sessionResponse = await retryApiCall(
            () => apiClient.post('/studies', { deck_id: props.deck.id }),
            3,
            'starting study session'
        );



        if (!sessionResponse.data || !sessionResponse.data.data || !sessionResponse.data.data.study_id) {
            console.error('Invalid response structure:', sessionResponse.data);
            throw new Error('Invalid response from study session creation - missing study_id');
        }

        studyId.value = sessionResponse.data.data.study_id;


        const cardsResponse = await retryApiCall(
            () => apiClient.get(`/decks/${props.deck.id}/cards`),
            3,
            'fetching cards'
        );



        if (!cardsResponse.data || !cardsResponse.data.data) {
            console.error('Invalid cards response structure:', cardsResponse.data);
            throw new Error('Invalid response from cards fetch - missing data array');
        }

        let fetchedCards = cardsResponse.data.data;

        fetchedCards = fetchedCards.sort(() => Math.random() - 0.5);

        if (requestedCardCount.value && requestedCardCount.value > 0) {
            const maxCards = Math.min(requestedCardCount.value, fetchedCards.length);
            fetchedCards = fetchedCards.slice(0, maxCards);
        }

        cards.value = fetchedCards;

        if (cards.value.length === 0) {
            sessionFinished.value = true;
        }
    } catch (err) {
        console.error("Failed to start study session:", err);
        if (err.response?.status === 401) {
            error.value = "Your session has expired. Please log in again.";
        } else if (err.response?.status === 403) {
            error.value = "You don't have permission to access this deck.";
        } else if (err.response?.status >= 500) {
            error.value = "Server error occurred. Please try again in a few moments.";
        } else {
            error.value = err.response?.data?.message || "An unexpected API error occurred.";
        }
    } finally {
        loading.value = false;
    }
});

const retryApiCall = async (apiCall, maxRetries = 3, operation = 'operation') => {
    let lastError;

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            return await apiCall();
        } catch (error) {
            lastError = error;

            // Don't retry on client errors (4xx) except 429 (rate limit)
            if (error.response?.status >= 400 && error.response?.status < 500 && error.response?.status !== 429) {
                throw error;
            }

            // Don't retry on the last attempt
            if (attempt === maxRetries) {
                break;
            }

            // Exponential backoff: wait 1s, 2s, 4s...
            const delay = Math.pow(2, attempt - 1) * 1000;
            console.warn(`API call attempt ${attempt} failed for ${operation}, retrying in ${delay}ms...`, error.message);
            await new Promise(resolve => setTimeout(resolve, delay));
        }
    }

    throw lastError;
};

// Methods
const revealAnswer = async () => {
    // Prevent multiple clicks while revealing
    if (isRevealingAnswer.value) {
        return;
    }

    isRevealingAnswer.value = true;

    // Add a small delay to prevent rapid clicking and provide visual feedback
    await new Promise(resolve => setTimeout(resolve, 300));

    answerVisible.value = true;
    isRevealingAnswer.value = false;
};

const recordResult = async (isCorrect) => {
    // Prevent multiple clicks while submitting
    if (isSubmittingResult.value) {
        return;
    }

    isSubmittingResult.value = true;

    if (isCorrect) {
        correctAnswers.value++;
    }

    // Validate that we have a valid study session
    if (!studyId.value) {
        console.error("No study session ID available");
        error.value = "Study session not properly initialized. Please refresh and try again.";
        isSubmittingResult.value = false;
        return;
    }

    // Validate that we have a current card
    if (!currentCard.value || !currentCard.value.id) {
        console.error("No current card available");
        error.value = "No card available to record result for.";
        isSubmittingResult.value = false;
        return;
    }

    try {
        await apiClient.post('/study-results', {
            study_id: studyId.value,
            card_id: currentCard.value.id,
            is_correct: isCorrect
        });

        // Check if this is the last card before moving to next
        // Don't reset loading state here - let nextCard/finishSession handle it
        if (currentCardIndex.value >= cards.value.length - 1) {
            finishSession();
        } else {
            nextCard();
        }
    } catch (err) {
        console.error("Failed to record result:", err);

        // Handle specific error codes with user-friendly messages
        if (err.response?.status === 401) {
            error.value = "Your session has expired. Please log in again.";
            localStorage.removeItem('api_token');
            window.location.href = '/login';
        } else if (err.response?.status === 403) {
            error.value = "You don't have permission to record this result. Please try logging in again.";
            localStorage.removeItem('api_token');
            window.location.href = '/login';
        } else if (err.response?.status === 422) {
            // Validation errors
            const validationErrors = err.response?.data?.errors;
            if (validationErrors?.study_id) {
                error.value = "Study session error. Please refresh the page and try again.";
                // Reset the study session
                studyId.value = null;
            } else if (validationErrors?.card_id) {
                error.value = "Card information is missing. Please refresh and try again.";
            } else {
                error.value = err.response?.data?.message || "Invalid data submitted.";
            }
        } else if (err.response?.status >= 500) {
            error.value = "Server error occurred. Please try again in a few moments.";
        } else {
            error.value = err.response?.data?.message || "Could not save your answer. Please check your connection and try again.";
        }
        // Reset loading state on error
        isSubmittingResult.value = false;
    }
};

const nextCard = () => {
    answerVisible.value = false;
    if (currentCardIndex.value < cards.value.length - 1) {
        currentCardIndex.value++;
        // Keep loading state active during transition
        setTimeout(() => {
            isSubmittingResult.value = false;
        }, 400); // Slightly longer than transition duration
    } else {
        finishSession();
    }
};

const finishSession = async () => {
    try {
        await apiClient.patch(`/studies/${studyId.value}/complete`);
        sessionFinished.value = true;
        // Keep loading state active during transition to completion screen
        setTimeout(() => {
            isSubmittingResult.value = false;
        }, 600); // Allow time for completion screen transition
    } catch (err) {
        console.error("Failed to complete session:", err);
        // This is a non-critical error, so we can still show the finished screen
        sessionFinished.value = true;
        setTimeout(() => {
            isSubmittingResult.value = false;
        }, 600);
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