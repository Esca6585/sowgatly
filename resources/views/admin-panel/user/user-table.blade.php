<div id="datatable">
    <table class="table table-separate table-head-custom table-checkable">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('First Name') }}</th>
                <th>{{ __('Last Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone number') }}</th>
                <th>{{ __('Address') }}</th>
                <th>{{ __('Are you Businessman?') }}</th>
                <th>{{ __('Company name') }}</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Created time') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sellers as $seller)
            <tr id="datatable">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $seller->first_name }}</td>
                <td>{{ $seller->last_name }}</td>
                <td>{{ $seller->email }}</td>
                <td>
                    <a href="tel:+993{{ $seller->phone_number }}">
                        <span>+993</span> {{ $seller->phone_number }}
                    </a>
                </td>
                <td>{{ $seller->address }}</td>
                <td>
                    @if($seller->roles->pluck("name")->first() == 'raýat')
                    <span class="badge badge-warning">{{ $seller->roles->pluck("name")->first() }}</span>
                    @elseif($seller->roles->pluck("name")->first() == 'telekeçi')
                    <span class="badge badge-primary">{{ $seller->roles->pluck("name")->first() }}</span>
                    @elseif($seller->roles->pluck("name")->first() == 'döwlet-edara')
                    <span class="badge badge-success">{{ $seller->roles->pluck("name")->first() }}</span>
                    @endif
                </td>
                <td>{{ $seller->company_name }}</td>
                <td>
                    @if($seller->deleted_at)
                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                    @else
                    <span class="badge badge-success">{{ __('Active') }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-secondary">{{ \Carbon::parse($seller->created_at)->locale(config('app.faker_locales.' . app()->getlocale() ))->isoFormat('LLLL') }}</span>
                </td>
                <td>@include('admin-panel.user.user-action', [ $seller ])</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end">
        <div>
            {{ $sellers->links('layouts.pagination') }}
        </div>
    </div>                                
</div>