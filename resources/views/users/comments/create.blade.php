@extends('users.show')

@include('generator::components.models.childs.create', [
  'resource_route' => 'users.comments',
  'model_variable' => 'comment',
  'parent' => $user
])
