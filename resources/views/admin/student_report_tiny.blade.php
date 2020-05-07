<head>
    <style>
        div {
            text-align: center;
        }

        .inline {
            display: inline-block;
            padding-right: 5px;
        }

        .bordered {
            border-right: 1px solid black;
            border-left: 1px solid black;
            padding-left: 5px;
        }

        table.center {
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<div>
    <span>{{ __('Student:') }}</span> <span>{{ $user->name }} {{ $user->identity }}</span>
</div>
<br />
<div>
    <table class="center">
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Time') }}</th>
        </tr>
        <tbody>
            @php
            $counter = 1;
            @endphp
            @foreach($lessons as $lesson)
            <tr>
                <td>
                    {{ $counter++ }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($lesson->date)->format('d-m-Y') }}
                </td>
                <td>
                    {{ $lesson->time }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>