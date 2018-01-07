<template>
    <li class="dropdown" v-if="notifications.length">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-bell"></span>
        </a>
        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a :href="notification.data.link" v-text="notification.data.message"
                   @click="markAsRead(notification)"></a>
            </li>
        </ul>
    </li>
</template>
<script>
    export default{
        created(){
            axios.get("/profiles/" + window.Laravel.user.name + "/notifications")
                .then(response => this.notifications = response.data);
        },
        data(){
            return {notifications: false}
        },
        methods: {
            markAsRead(notification){
                axios.delete("/profiles/" + window.Laravel.user.name + "/notifications/" + notification.id)
            }
        }
    }
</script>