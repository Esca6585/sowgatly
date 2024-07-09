<div id="datatable">
    <table class="table table-separate table-head-custom table-checkable">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Phone number') }}</th>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Created time') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sellers as $seller)
            <tr id="datatable">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $seller->name }}</td>
                <td>
                    <a href="tel:+993{{ $seller->phone_number }}">
                        <span>+993</span> {{ $seller->phone_number }}
                    </a>
                </td>
                <td><img src="{{ asset($seller->image) }}" alt="{{ asset($seller->image) }}" class="logo-circle"></td>
                <td>
                    @if($seller->status)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                    @else
                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-secondary">{{ \Carbon::parse($seller->created_at)->locale(config('app.faker_locales.' . app()->getlocale() ))->isoFormat('LLLL') }}</span>
                </td>
                <td>@include('admin-panel.seller.seller-action', [ $seller ])</td>
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