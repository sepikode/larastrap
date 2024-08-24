<div class="btn-group dropstart">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle d-print-none" data-bs-toggle="dropdown" aria-expanded="false">
        {{ __('Aksi') }}
    </button>
    <ul class="dropdown-menu">

        @can($permission_edit)
            <li><a class="dropdown-item d-print-none" href="{{ $url_edit }}"><i class="fa-solid fa-pen-to-square"></i> {{ __(' Edit') }}</a></li>
        @endcan
        @can($permission_delete)
            <li>
                <button type="button" class="dropdown-item d-print-none deleteItem" data-id="{{ $data }}"
                    data-name="{{ $name }}"><i class="far fa-trash-alt"></i> {{ __(' Hapus') }}</button>
            </li>
        @endcan
    </ul>
</div>

