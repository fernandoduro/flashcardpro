<template>
    <div class="p-6 bg-white border-b border-gray-200 rounded-lg shadow-md max-w-2xl mx-auto">
        <div v-if="!sessionFinished">
            <div v-if="currentCard">
                <div class="mb-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">{{ deckName }}</h2>
                    <span class="text-gray-500">{{ currentCardIndex + 1 }} / {{ cards.length }} cards</span>
                </div>
                <div class="p-8 border rounded-lg text-center">
                    <p class="text-2xl mb-6">{{ currentCard.question }}</p>

                    <div v-if="!answerVisible">
                        <button @click="revealAnswer" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Reveal Answer</button>
                    </div>

                    <div v-else>
                        <p class="text-3xl font-bold mb-8 text-green-600">{{ currentCard.answer }}</p>
                        <div class="flex justify-center space-x-4">
                            <button @click="recordResult(true)" class="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600">I got it right!</button>
                            <button @click="recordResult(false)" class="px-6 py-2 bg-red-500 text-white rounded hover:bg-red-600">Maybe next time...</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                Loading...
            </div>
        </div>
        <div v-else class="text-center">
            <h2 class="text-2xl font-bold mb-4">Session Complete!</h2>
            <p class="text-lg">You got {{ correctAnswers }} out of {{ cards.length }} correct.</p>
            <a :href="deckUrl" class="mt-6 inline-block px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Back to Deck</a>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    deck: Object,
});

const cards = ref([]);
const currentCardIndex = ref(0);
const currentCard = ref(null);
const answerVisible = ref(false);
const sessionFinished = ref(false);
const correctAnswers = ref(0);
const studyId = ref(null);

const deckName = props.deck.name;
const deckUrl = `/decks/${props.deck.id}`;

onMounted(async () => {
    // 1. Start a new study session
    const sessionResponse = await axios.post('/api/studies', { deck_id: props.deck.id });
    studyId.value = sessionResponse.data.study_id;

    // 2. Fetch the cards for the deck (shuffled)
    const cardsResponse = await axios.get(`/api/decks/${props.deck.id}/cards`);
    cards.value = cardsResponse.data.data;
    if (cards.value.length > 0) {
        currentCard.value = cards.value[0];
    } else {
        sessionFinished.value = true;
    }
});

const revealAnswer = () => {
    answerVisible.value = true;
};

const recordResult = async (isCorrect) => {
    if (isCorrect) {
        correctAnswers.value++;
    }
    // Record the result
    await axios.post('/api/study-results', {
        study_id: studyId.value,
        card_id: currentCard.value.id,
        is_correct: isCorrect
    });
    nextCard();
};

const nextCard = () => {
    answerVisible.value = false;
    if (currentCardIndex.value < cards.value.length - 1) {
        currentCardIndex.value++;
        currentCard.value = cards.value[currentCardIndex.value];
    } else {
        finishSession();
    }
};

const finishSession = async () => {
    await axios.patch(`/api/studies/${studyId.value}/complete`);
    sessionFinished.value = true;
};

</script>