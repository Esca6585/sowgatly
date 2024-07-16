<div id="datatable">
    <table class="table table-separate table-head-custom table-checkable">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Username') }}</th>
                <th>{{ __('Shop name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone number') }}</th>
                <th>{{ __('Address') }}</th>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Created time') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shops as $shop)
            <tr id="datatable">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $shop->seller->name }}</td>
                <td>{{ $shop->name }}</td>
                <td>{{ $shop->email }}</td>
                <td>
                    <a href="tel:+993{{ $shop->seller->phone_number }}">
                        <span>+993</span> {{ $shop->seller->phone_number }}
                    </a>
                </td>
                <td>{{ $shop->address }}</td>
                <td><img src="{{ asset($shop->image) }}" alt="{{ asset($shop->image) }}" class="logo-circle"></td>
                <td>
                    @if($shop->seller->status)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                    @else
                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-secondary">{{ \Carbon::parse($shop->created_at)->locale(config('app.faker_locales.' . app()->getlocale() ))->isoFormat('LLLL') }}</span>
                </td>
                <td>@include('admin-panel.shop.shop-action', [ $shop ])</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end">
        <div>
            {{ $shops->links('layouts.pagination') }}
        </div>
    </div>                                
</div>