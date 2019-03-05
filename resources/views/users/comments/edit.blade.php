@extends('users.show')

@include('generator::components.models.childs.edit', [
  'resource_route' => 'users.comments',
  'model_variable' => 'comment',
  'parent' => $user,
  'model' => $comment
])
