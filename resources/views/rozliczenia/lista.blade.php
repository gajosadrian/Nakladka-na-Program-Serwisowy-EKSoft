@extends('global.app')

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rozliczenia</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-row>
            <b-col lg="7">
                <b-block>
                    <template slot="content">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
        						<thead>
        							<tr class="thead-light">
                                        <th>Okres</th>
                                        <th>Suma robocizn netto</th>
                                        <th>Suma dojazdów netto</th>
                                        <th>Wystawił</th>
                                        <th>Działania</th>
        								<th class="d-none"></th>
        							</tr>
        						</thead>
        						<tbody>
        						</tbody>
                            </table>
                        </div>
                    </template>
                </b-block>
            </b-col>
            <b-col lg="5"></b-col>
        </b-row>
    </div>
@endsection
