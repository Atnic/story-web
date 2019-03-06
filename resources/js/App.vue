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
}

.v-toolbar__content {
  border-bottom: 1px solid #b1b6ba;
}

a {
  text-decoration: none;
  color: black !important;
}

.content {
  margin-top: 56px;
  height: calc(100% - 56px);
}

.post-content {
  padding: 16px 5px;
}

.post-content > div {
  margin: 0 5px;
}

p.article {
  line-height: 1.3125;
}

.post, .comment {
  cursor: pointer;
}

@media screen and (min-width: 560px){
  .application--wrap {
    background-color: #b1b6ba;
  }

  .application--wrap > div {
    width: 560px;
    padding: 0 16px;
    margin: 0 auto;
    background-color: white;
  }
}
</style>

