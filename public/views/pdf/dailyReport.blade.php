<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
    <title>Coupe du Monde de la FIFA, Russie 2018™</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">


        <style>
            html, body{
                font-family: 'Oswald', sans-serif;
            }
            .print, .print-img{
                width: 1300px;
                height: 1839px;
                position: relative;
            }
            .print-img{
                position: absolute;
            }
            .toprank{
                position: relative;
                height: 480px;
            }

            .toprank span{
                position:absolute;
            }

            .toprank .date{
                top:220px;
                left:350px;
                color: #fff;
                font-size: 35px;
            }

            .toprank .first, .toprank .second, .toprank .third{
                color: #232323;
                font-size: 20px;
                text-align: center;
                width: 155px;
                display: block;
            }

            .toprank .first{
                top:350px;
                left:886px;
            }

            .toprank .second{
                top:350px;
                left:720px;
            }

            .toprank .third{
                top:350px;
                left:1055px;
            }

            .games{
                height: 380px;
                padding-left:40px;
                margin-top:96px;
            }

            .games .card{
                margin:10px;
            }

            .games .flag{
                width: 100px;
                height: 50px;
            }

            .games .score{
                font-size:30px;
                text-align: center;
            }

            .mvp{
                height: 380px;
                position: relative;
            }

            .mvp .first, .mvp .second{
                position: absolute;
                color:#fff;
                font-size: 26px;
                left:820px;
            }

            .mvp .first span, .mvp .second span{
                color: #08b068;
                font-size: 0.8em;
                top:60px;
            }
            .mvp .first{
                font-size:40px;
                left:360px;
                width: 300px;
                text-align: right;
                line-height: 80px;

            }
            .mvp .second{
                line-height: 65px;
            }

            .ranking{
                margin-top:20px;
            }

            .ranking ul{
                list-style: none;
                color: #fff;
            }

            .ranking ul .rank{
                width:40px;
                background: #b52118;
                padding: 6px;
                display: inline-block;
                margin:2px;
                text-align: center;
            }

            .ranking ul .evolution{
                font-size: 0.7em;
            }

            .ranking ul img{
                width: 20px;
                height: 20px;
                margin-left: 10px;
                margin-top:-2px;
            }
        </style>

    </head>
<body>
<div class="print">
    <img src="/images/report/background2.jpg" class="print-img">
    <div class="container-fluid">
        <div class="row toprank" >
            <span class="date">{{date_format($date, 'd/m/Y')}}</span>

            @if(isset($users[0]))
                <span class="first"><b>{{$users[0]['firstname']}} {{$users[0]['lastname']}}.</b><br/> {{$users[0]['winPoints']}}XP</span>
            @endif
            @if(isset($users[1]))
                <span class="second"><b>{{$users[1]['firstname']}} {{$users[1]['lastname']}}.</b><br/> {{$users[1]['winPoints']}}XP</span>
            @endif
            @if(isset($users[2]))
                <span class="third"><b>{{$users[2]['firstname']}} {{$users[2]['lastname']}}.</b><br/> {{$users[2]['winPoints']}}XP</span>
            @endif
        </div>
        <div class="row games">
            <div class="col-8">
                <div class="row">

                    @if(isset($games[0]))
                        <div class="card col-5  ml-1" >
                            <div class="card-body">
                                <div class="row">
                                    <img src="/images/flags/{{strtolower($games[0]['team1']['code'])}}" class="flag">
                                    <div class="col-4 score">{{$games[0]['team1_points']}} - {{$games[0]['team2_points']}}</div>
                                    <img src="/images/flags/{{strtolower($games[0]['team2']['code'])}}" class="flag">
                                </div>
                                <div class="row">
                                    <div class="col-6">{{$games[0]['team1']['name']}}</div>
                                    <div class="col-6" style="text-align: right;">{{$games[0]['team2']['name']}}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($games[1]))
                        <div class="card col-5  ml-1" >
                            <div class="card-body">
                                <div class="row">
                                    <img src="/images/flags/{{strtolower($games[1]['team1']['code'])}}" class="flag">
                                    <div class="col-4 score">{{$games[1]['team1_points']}} - {{$games[1]['team2_points']}}</div>
                                    <img src="/images/flags/{{strtolower($games[1]['team2']['code'])}}" class="flag">
                                </div>
                                <div class="row">
                                    <div class="col-6">{{$games[1]['team1']['name']}}</div>
                                    <div class="col-6" style="text-align: right;">{{$games[1]['team2']['name']}}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="row">

                    @if(isset($games[2]))
                        <div class="card col-5  ml-1" >
                            <div class="card-body">
                                <div class="row">
                                    <img src="/images/flags/{{strtolower($games[2]['team1']['code'])}}" class="flag">
                                    <div class="col-4 score">{{$games[2]['team1_points']}} - {{$games[2]['team2_points']}}</div>
                                    <img src="/images/flags/{{strtolower($games[2]['team2']['code'])}}" class="flag">
                                </div>
                                <div class="row">
                                    <div class="col-6">{{$games[2]['team1']['name']}}</div>
                                    <div class="col-6" style="text-align: right;">{{$games[2]['team2']['name']}}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($games[3]))
                        <div class="card col-5  ml-1" >
                            <div class="card-body">
                                <div class="row">
                                    <img src="/images/flags/{{strtolower($games[3]['team1']['code'])}}" class="flag">
                                    <div class="col-4 score">{{$games[3]['team1_points']}} - {{$games[3]['team2_points']}}</div>
                                    <img src="/images/flags/{{strtolower($games[3]['team2']['code'])}}" class="flag">
                                </div>
                                <div class="row">
                                    <div class="col-6">{{$games[3]['team1']['name']}}</div>
                                    <div class="col-6" style="text-align: right;">{{$games[3]['team2']['name']}}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>


            </div>
        </div>
    </div>
    @if(isset($users[0]))
        <span class="first"><b>{{$users[0]['firstname']}} {{$users[0]['lastname']}}.</b><br/> {{$users[0]['winPoints']}}XP</span>
    @endif

    <div class="mvp">
        @if(isset($MVP[0]))
            <div class="first"> {{$MVP[0]['firstname']}} {{$MVP[0]['lastname']}}. <br><span>+{{$MVP[0]['winPointsLastDay']}}XP</span></div>
        @endif

        <div class="second">
            @for ($i = 1; $i < 5; $i++)
                @if(isset($MVP[$i]))
                    #{{$i+1}} {{$MVP[$i]['firstname']}} {{$MVP[$i]['lastname']}}. <span>+{{$MVP[$i]['winPointsLastDay']}}XP</span><br />
                @endif
            @endfor
        </div>
    </div>


    <div class="ranking">
        <div class="row">
            <div class="col">
                <ul>
                    @for ($i = 0; $i < 10; $i++)
                        @if(isset($users[$i]))
                            <li @if($i == 0)style="font-size: 2em"@endif @if($i == 1)style="font-size: 1.6em"@endif @if($i == 2)style="font-size: 1.3em"@endif ><span class="rank" >#{{$i+1}}</span> @if($users[$i]['diffPlace']>0)<img src="/images/report/up.jpg">@endif @if($users[$i]['diffPlace']<0)<img src="/images/report/down.jpg">@endif {{$users[$i]['firstname']}} {{$users[$i]['lastname']}}. <span class="evolution">{{$users[$i]['winPoints']}}XP {{($users[$i]['winPointsLastDay'] > 0)?'+'.$users[$i]['winPointsLastDay'].'PX':''}}</span></li>
                        @endif
                    @endfor
                </ul>
            </div>

            @if(isset($users[10]))
                <div class="col">
                    <ul>
                        @for ($i = 10; $i <= 20; $i++)
                            @if(isset($users[$i]))
                                <li><span class="rank" >#{{$i+1}}</span> @if($users[$i]['diffPlace']>0)<img src="/images/report/up.jpg">@endif @if($users[$i]['diffPlace']<0)<img src="/images/report/down.jpg">@endif {{$users[$i]['firstname']}} {{$users[$i]['lastname']}}. <span class="evolution">{{$users[$i]['winPoints']}}XP {{($users[$i]['winPointsLastDay'] > 0)?'+'.$users[$i]['winPointsLastDay'].'PX':''}}</span></li>
                            @endif
                        @endfor
                    </ul>
                </div>
            @endif

            @if(isset($users[21]))
                <div class="col">
                    <ul>
                        @for ($i = 21; $i <= 31; $i++)
                            @if(isset($users[$i]))
                                <li><span class="rank" >#{{$i+1}}</span> @if($users[$i]['diffPlace']>0)<img src="/images/report/up.jpg">@endif @if($users[$i]['diffPlace']<0)<img src="/images/report/down.jpg">@endif {{$users[$i]['firstname']}} {{$users[$i]['lastname']}}. <span class="evolution">{{$users[$i]['winPoints']}}XP {{($users[$i]['winPointsLastDay'] > 0)?'+'.$users[$i]['winPointsLastDay'].'PX':''}}</span></li>
                            @endif
                        @endfor
                    </ul>
                </div>
            @endif

            <div class="col-2"></div>

        </div>

    </div>


</div>
</body>
</html>