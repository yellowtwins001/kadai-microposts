@if (Auth::user()->is_favorite($micropost->id))
    {!! Form::open(['route' => ['post.unfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('Unfavorite', ['class' => 'btn btn-success btn-xs']) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(['route' => ['post.favorite', $micropost->id]]) !!}
        {!! Form::submit('Favorite', ['class' => 'btn btn-default btn-xs']) !!}
    {!! Form::close() !!}
@endif