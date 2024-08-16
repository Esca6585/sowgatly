<div id="datatable">
    <table class="table table-separate table-head-custom table-checkable">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Username') }}</th>
                <th>{{ __('Region') }} {{ __('Name') }}</th>
                <th>{{ __('Address') }} {{ __('Street') }}</th>
                <th>{{ __('Address') }} {{ __('City') }}</th>
                <th>{{ __('Address') }} {{ __('State') }}</th>
                <th>{{ __('Address') }} {{ __('Country') }}</th>
                <th>{{ __('Address') }} {{ __('Postal Code') }}</th>
                <th>{{ __('Created time') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regions as $region)
            <tr id="datatable">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $region->name }}</td>
                <td>{{ $region->parent->name }}</td>
                <td>{{ $region->address->street }}</td>
                <td>{{ $region->address->city }}</td>
                <td>{{ $region->address->state }}</td>
                <td>{{ $region->address->country }}</td>
                <td>{{ $region->address->postal_code }}</td>
                <td>
                    <span class="badge badge-secondary">{{ \Carbon::parse($region->created_at)->locale(config('app.faker_locales.' . app()->getlocale() ))->isoFormat('LLLL') }}</span>
                </td>
                <td>@include('admin-panel.region.region-action', [ $region ])</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end">
        <div>
            {{ $regions->links('layouts.pagination') }}
        </div>
    </div>                                
</div>