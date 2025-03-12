@extends('layout.template')

@section('content')
    <div class="class-card container mt-4">
        <h2 class="text-center mb-4">Daftar Mahasiswa</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>1</td>
                        <td>Zidni kesatu</td>
                        <td>111</td>
                        <td>A</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Zidni kedua</td>
                        <td>112</td>
                        <td>A</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Bukan Zidni hehe</td>
                        <td>1113</td>
                        <td>B</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Ini Zidni</td>
                        <td>114</td>
                        <td>B</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>ZIZIZI</td>
                        <td>1113</td>
                        <td>B</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Yah begini Zidni</td>
                        <td>116</td>
                        <td>C</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Zidni deh</td>
                        <td>117</td>
                        <td>C</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Selamat Tinggal Zidni</td>
                        <td>118</td>
                        <td>A</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
