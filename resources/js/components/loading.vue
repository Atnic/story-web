<template>
    <v-layout justify-center>
        <v-progress-circular
            v-if="$store.state.loadContent"
            indeterminate
            color="blue"
            width="3"
            size=28
        ></v-progress-circular>
    </v-layout>
</template>
