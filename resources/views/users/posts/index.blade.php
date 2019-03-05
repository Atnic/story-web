@extends('users.show')

@include('generator::components.models.childs.index', [
  'resource_route' => 'users.posts',
  'model_variable' => 'post',
  'model_class' => \App\Post::class,
  'parent' => $user,
  'models' => $posts,
  'action_buttons_view' => 'generator::components.models.childs.index.action_buttons',
])
