<!DOCTYPE html>
<html>
<head>
	<title>Posts</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>Posts Recap</h4>
        </center>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>Nama Penulis</th>
				<th>Tanggal</th>
				<th>Judul</th>
				<th>Kategori</th>
				{{-- <th>Tag</th> --}}
				<th>Isi</th>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($posts as $post)
			<tr>
				<td>{{ $i++ }}</td>
				<td>{{ $post->user_id }}</td>
				<td>{{ $post->created_at->format("d F, Y") }}</td>
				<td>{{  $post->title  }}</td>
				<td>{{ $post->category_id }}</td>
				{{-- @foreach($post->tags as $tag)
					<td>{{ $tag->name }}</td>
                @endforeach --}}
				<td>{{ $post->body }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
 
</body>
</html>