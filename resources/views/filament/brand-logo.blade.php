@if (request()->is('admin/login'))

<div style="
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    margin-bottom:100px;
">
    <img
        src="{{ asset('images/sma-nurul-fikri-logo.jpeg') }}"
        alt="Logo SMA NURUL FIKRI"
        style="
            width:200px;
            height:auto;
        "
    >

    <div>
        <div style="
            color:#3f8a6a;
            font-size:35px;
            font-weight:800;
            line-height:1;
        ">
            SIAKAD
        </div>

        <div style="
            color:#06111f;
            font-size:40px;
            font-weight:600;
            line-height:1.1;
            white-space:nowrap;
        ">
            <strong>SMA NURUL FIKRI</strong>
        </div>
    </div>
</div>

@else

<div style="
    display:flex;
    align-items:center;
    gap:10px;
">
    <img
        src="{{ asset('images/sma-nurul-fikri-logo.jpeg') }}"
        alt="Logo SMA NURUL FIKRI"
        style="width:60px;height:auto;"
    >

    <div>
        <h3 style="
            margin:0;
            font-size:18px;
            font-weight:500;
            color:#3f8a6a;
        ">
            Sistem Informasi Akademik
        </h3>

        <h4 style="
            margin:0;
            font-size:11px;
            font-weight:400;
            color:#06111f;
        ">
            SMA NURUL FIKRI
        </h4>
    </div>
</div>

@endif