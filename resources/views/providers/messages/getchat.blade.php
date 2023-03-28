
					@php
						$last_id=0;
                            $i=1;
					@endphp
					@foreach($objects as $object)
						@php
							if($i==1){
                            $last_id = $object->id;
                            }

						@endphp
						@if($object->sender_id == 1)
							<div style="background-color: #cccccc52;border: grey 1px solid;border-radius: 10px" class="col-xs-9 " message_id="{{ $object->id }}">
								<p>{{ $object->message }} {!!   $object->image ? "<br><img src='/uploads/".$object->image."' style='width:150px;height:150px;' />" : "" !!}</p>
								<br>
								<span>Admin</span>
								<br>
								<span>{{ $object->created_at->diffForHumans() }}</span>
							</div>
							<div class="clearfix"></div>
							<br>
						@else
							<div style="background-color: #cccccc52;border: grey 1px solid;border-radius: 10px" class="col-xs-9 col-xs-offset-3 pull-left" message_id="{{ $object->id }}">
								<p>{{ $object->message }} {!! $object->image ? "<br><img src='/uploads/".$object->image."' style='width:150px;height:150px;' />" : "" !!}</p>
								<br>
								<span>{{ @$object->getSenderUser->first_name." ". @$object->getSenderUser->last_name }}</span>
								<br>
								<span>{{ $object->created_at->diffForHumans() }}</span>
							</div>
							<div class="clearfix"></div>
							<br>
							@php
								$object->status=1;
                                $object->save();
							@endphp
						@endif
						@php
							$i++;
						@endphp
					@endforeach
