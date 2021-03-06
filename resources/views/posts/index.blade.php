@extends('layouts.app')

@section('content-title', ucwords(__('posts.plural')))

@include('generator::components.models.index', [
  'col_class' => 'col-md-12',
  'panel_title' => ucwords(__('posts.plural')),
  'resource_route' => 'posts',
  'model_variable' => 'post',
  'model_class' => \App\Post::class,
  'models' => $posts,
  'action_buttons_view' => 'generator::components.models.index.action_buttons',
])
