<template>
    <v-app>
        <router-view></router-view>
    </v-app>
</template>

<script>
    export default {
        name: "App",
        data() {
            return {
                //
            };
        }
    };
</script>

<style>
    .application--wrap {
        background-color: white;
        min-height: 100vh;
    }
    .v-toolbar__content {
        padding: 0 10px;
        border-bottom: 1px solid rgba(0,0,0,0.12);
    }
    a {
        text-decoration: none;
        color: black !important;
    }
    .item-content a {
        color: #2196f3 !important;
    }
    .item-content a:hover {
        text-decoration: underline;
    }
    .item-content img {
        max-width: 100% !important;
        display: block;
        border-radius: 4px;
        margin: 4px 0;
    }
    .content {
        margin-top: 49px;
        height: calc(100% - 49px);
    }
    p.article {
        line-height: 1.3125;
    }
    .post, .comment {
        cursor: pointer;
    }
    .v-toolbar,
    .v-toolbar__content {
        height: 49px !important;
    }
    @media screen and (min-width: 560px){
        .application--wrap {
            background-color: #b1b6ba;
        }
        .application--wrap > div {
            width: 560px;
            min-height: 100vh;
            padding: 0;
            margin: 0 auto;
            background-color: white;
        }
    }

    code {
        color: #24292e!important;
        box-shadow: none!important;
        font-weight: 400!important;
    }

    .markdown-body {
        display: grid;
    }
</style>
