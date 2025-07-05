<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    title: String,
    description: String
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
    </Head>

    <div class="hobby-container">
        <div class="section-container">
            <h1 class="page-title">Anton F.'s Hobbies</h1>
            <div class="hobby-content">
                <div class="hobby-text">
                    <p>Hobbies and interests content coming soon...</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hobby-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
    color: #ffffff;
    font-family: 'Arial', 'Helvetica', sans-serif;
    padding: 100px 5%;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    font-size: 3rem;
    font-weight: 600;
    margin-bottom: 60px;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hobby-content {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 40px;
}

.hobby-text {
    font-size: 1.2rem;
    line-height: 1.6;
    color: #a0a0a0;
}
</style>