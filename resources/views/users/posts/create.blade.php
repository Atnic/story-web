@extends('users.show')

@include('generator::components.models.childs.create', [
  'resource_route' => 'users.posts',
  'model_variable' => 'post',
  'parent' => $user
])
