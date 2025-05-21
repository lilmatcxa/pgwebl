@extends('layout.template')

@section('content')
    <div class="class-card container mt-4">
        <h2 class="text-center mb-4">Daftar Data Spasial (Point, Polyline, Polygon)</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @php $no = 1; @endphp

                    {{-- Data Point --}}
                    @foreach ($points as $point)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>Point</td>
                            <td>{{ $point->name }}</td>
                            <td>{{ $point->description }}</td>
                            <td>
                                <img src="{{ asset('storage/images/' . $point->image) }}" width="200px"
                                    alt="{{ $point->name }}" title="Photo of {{ $point->name }}">
                            </td>
                            <td>{{ $point->created_at }}</td>
                            <td>{{ $point->updated_at }}</td>
                        </tr>
                    @endforeach

                    {{-- Data Polyline --}}
                    @foreach ($polylines as $polyline)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>Polyline</td>
                            <td>{{ $polyline->name }}</td>
                            <td>{{ $polyline->description }}</td>
                            <td>
                                <img src="{{ asset('storage/images/' . $polyline->image) }}" width="200px"
                                    alt="{{ $polyline->name }}" title="Photo of {{ $polyline->name }}">
                            </td>
                            <td>{{ $polyline->created_at }}</td>
                            <td>{{ $polyline->updated_at }}</td>
                        </tr>
                    @endforeach

                    {{-- Data Polygon --}}
                    @foreach ($polygons as $polygon)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>Polygon</td>
                            <td>{{ $polygon->name }}</td>
                            <td>{{ $polygon->description }}</td>
                            <td>
                                <img src="{{ asset('storage/images/' . $polygon->image) }}" width="200px"
                                    alt="{{ $polygon->name }}" title="Photo of {{ $polygon->name }}">
                            </td>
                            <td>{{ $polygon->created_at }}</td>
                            <td>{{ $polygon->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
