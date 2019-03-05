@extends('users.show')

@include('generator::components.models.childs.index', [
  'resource_route' => 'users.comments',
  'model_variable' => 'comment',
  'model_class' => \App\Comment::class,
  'parent' => $user,
  'models' => $comments,
  'action_buttons_view' => 'generator::components.models.childs.index.action_buttons',
])
