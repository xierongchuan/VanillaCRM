@extends('layouts.main')

@section('title', 'Администраторы')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h2 class="text-center mt-4">Создать Администратора</h2>
            <form action="{{ route('admin.store') }}" method="post" class="bg-body-tertiary rounded p-3">
                @csrf

                <div class="form-group mb-2">
                    <label for="login">Логин:</label>
                    <input type="text" autocomplete="off" class="form-control" id="login" name="login" required>
                </div>
                <div class="form-group mb-2">
                    <label for="password">Имя:</label>
                    <input type="password" autocomplete="off" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group mb-2">
                    <label for="full_name">Пароль:</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>

            <div class="p-2 border bg-body-tertiary rounded p-3 mt-2">
                <span class="lead">Администраторы: </span><br>
                <div class="mt-2">
                    <item-list
                        :items='@json($admins->map(function($admin) { return ["id" => $admin->id, "full_name" => $admin->full_name]; })->values())'
                        :actions='[
                            { icon: "bi bi-trash", variant: "danger", href: (item) => `/admin/${item.id}/delete`, confirm: true, confirmMessage: "Вы уверены, что хотите удалить администратора?" }
                        ]'
                        empty-text="Нет администраторов"
                    >
                        <template #item="{ item }">
                            <a href="#" class="tw-text-xl tw-font-medium tw-text-gray-800 dark:tw-text-gray-200 tw-no-underline">@{{ item.full_name }}</a>
                        </template>
                    </item-list>
                </div>
            </div>
        </div>
    </div>

@endsection
