<script>
    var a = {};

    a.a = '';
    a.b = '';
    a.d = '';
    a.e = '';
    a.f = '';
    a.g = '';

    a.e = 'e';
    a.f = 't';
    a.a = 'a';
    a.d = 'd';

    for (var i in a) {
        console.log(i, a[i])
    }

</script>