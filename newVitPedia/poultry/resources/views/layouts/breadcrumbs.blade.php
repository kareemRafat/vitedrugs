<div class="breadcrumb-header justify-content-between align-items-center mb-4">

    <div class="w-100">

        <nav aria-label="breadcrumb">

            <ol class="breadcrumb bg-white shadow-sm rounded px-3 py-3 mb-0 align-items-center flex-wrap">

                <li class="breadcrumb-item">

                    <a href="{{ route('home') }}"
                       class="text-primary d-flex align-items-center">

                        <i class="fa fa-home mr-2"></i>

                        <span>Home</span>

                    </a>

                </li>

                @foreach($items as $item)

                    @if(isset($item['url']))

                        <li class="breadcrumb-item">

                            <a href="{{ $item['url'] }}"
                               class="text-primary">

                                {{ $item['title'] }}

                            </a>

                        </li>

                    @else

                        <li class="breadcrumb-item active font-weight-bold text-dark"
                            aria-current="page">

                            {{ $item['title'] }}

                        </li>

                    @endif

                @endforeach

            </ol>

        </nav>

    </div>

</div>