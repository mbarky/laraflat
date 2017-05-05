@extends('admin.layout.app')

@section('title')
    category {{  isset($item) ? ucfirst('edit') : ucfirst('add') }}
@endsection

@section('content')
    @component('admin.layout.form' , ['title' => 'categorie' , 'action' => isset($item) ? 'edit' : 'add' ])
        @include('admin.layout.messages')
        <form action="{{ url('admin/categorie/item') }}/{{ isset($item) ? $item->id : '' }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="form-line">
                    <input type="text" name="name" id="name" class="form-control" value="{{ isset($item) ? $item->name : '' }}"/>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-default" >
                    <i class="material-icons">check_circle</i>
                    save category
                </button>
            </div>
        </form>
    @endcomponent
@endsection
