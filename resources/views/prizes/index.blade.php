@extends('default')

@section('content')
    @include('prob-notice')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex float-end">
                    <a href="{{ route('prizes.create') }}">
                        <button class="btn btn-info">
                            {{ __('message.buttons.addNew')}} 
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </button>
                    </a>
                </div>
                <h1>{{ __('message.prizes')}}</h1>
                <table class="table table-bordered table-striped" id="myTable">
                    <thead>
                        <tr>
                            <th>{{ __('message.id')}} </th>
                            <th>{{ __('message.labels.title')}}</th>
                            <th>{{ __('message.labels.probability')}}</th>
                            <th>{{ __('message.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>
                                    <div class="float-end">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                        <a href="{{ route('prizes.destroy', [$prize->id]) }}" class="btn btn-danger"><i class="fa fa-ban" aria-hidden="true"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row container mt-5">
        <div class="col-md-12">
            @include('simulation')
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <!-- probability reward graph-->
            <div class="col-md-6 probability-graph">
                <div class="card">
                    <h2 class="card-header">{{ __('message.probabilitySettings') }}</h2>
                    <div class="card-body">
                        <canvas id="probabilityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- awarded reward graph-->
            <div class="col-md-6 awarded-reward" style="display: none;">
                <div class="card">
                    <h2 class="card-header">{{ __('message.actualReward')}}</h2>
                    <div class="card-body">
                        <canvas id="awardedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{asset('/js/index.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        $(document).ready(function () {
            // when click on simulate button then need to remove css
            $('#simulationButton').click(function () {
                $('.awarded-reward').css('display','block');
            });
        });
    </script>
@endpush
