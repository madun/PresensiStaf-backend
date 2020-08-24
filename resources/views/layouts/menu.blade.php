<div class="col-md-2">
    <div class="menu">
        <a href="{{ route('employee.get') }}" class="item-menu d-flex color-red {{ setActive(['employee*']) }}">
            <i data-feather="users"></i>
            <span>Data Staf</span>
        </a>
        <a class="item-menu d-flex color-green {{ setActive(['presensi*']) }}">
            <i data-feather="calendar"></i>
            <span>Presensi</span>
        </a>
        <a href="{{ route('sick.index') }}" class="item-menu d-flex color-blue {{ setActive(['sick*']) }}">
            <i data-feather="activity"></i>
            <span>Sakit</span>
        </a>
        <a class="item-menu d-flex color-orange {{ setActive(['leave*']) }}">
            <i data-feather="clipboard"></i>
            <span>Cuti</span>
        </a>
        <a class="item-menu d-flex color-red {{ setActive(['permit*']) }}">
            <i data-feather="flag"></i>
            <span>Izin</span>
        </a>
        <a href="{{ route('periode.index') }}" class="item-menu d-flex color-red {{ setActive(['periode*']) }}">
            <i data-feather="settings"></i>
            <span>Periode</span>
        </a>
        <a href="{{ url('maps') }}" class="item-menu d-flex color-red {{ setActive(['maps*']) }}">
            <i data-feather="settings"></i>
            <span>Maps</span>
        </a>
    </div>
</div>


@section('style')
<style>
    .menu {
        border: 1px solid rgb(236, 236, 236);
        background: #fff;
        border-radius: 6px;
    }

    .menu > .item-menu {
        padding: 14px 16px;
        border-bottom: 1px solid #f4f4f4;
        align-items: center;
    }

    .menu > .item-menu:last-child {
        border-bottom: none;
    }

    .item-menu.active {
        background: #f7f7f7;
    }

    .item-menu > span {
        color: #535353;
        font-weight: bold;
    }

    .item-menu > svg {
        margin-right: 12px;
    }

    .color-red > svg {
        color: #df5757;
    }

    .color-green > svg {
        color: #38d345;
    }

    .color-blue > svg {
        color: #53aaf1;
    }

    .color-orange > svg {
        color: #e76f1f;
    }

    .form-nonformal-css svg, .kehidupan-berorganisasi svg, .pengalaman-bekerja svg, .pengalaman-mengajar svg {
        color: #df5757;
        cursor: pointer;
    }
</style>
@endsection