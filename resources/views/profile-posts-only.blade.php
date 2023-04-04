<div class="list-group">
    @foreach ($post as $p)
        <x-post :post="$p" hideAuthor />
    @endforeach
</div>
