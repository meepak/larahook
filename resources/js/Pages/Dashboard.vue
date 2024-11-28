<template>
    <div>
        <h1>Welcome, {{ username }}</h1>
        <div v-for="request in requests" :key="request.id">
            <h3>Request #{{ request.id }}</h3>
            <pre>{{ request.request_data }}</pre>
            <div v-if="request.files.length > 0">
                <h4>Attachments:</h4>
                <ul>
                    <li v-for="file in request.files" :key="file.stored_path">
                        <a :href="file.stored_path">{{ file.original_name }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        username: String,
        requests: Array
    }
}
</script>
