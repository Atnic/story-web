<template>
    <v-layout justify-center>
        <v-progress-circular
            v-if="$store.state.loadContent"
            indeterminate
            color="blue"
            width="5"
            style="margin: 32px auto;"
        ></v-progress-circular>
    </v-layout>
</template>
