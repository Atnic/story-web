@extends('users.show')

@include('generator::components.models.childs.show', [
  'resource_route' => 'users.posts',
  'model_variable' => 'post',
  'parent' => $user,
  'model' => $post
])
