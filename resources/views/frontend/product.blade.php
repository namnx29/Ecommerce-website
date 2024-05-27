@extends('frontend.layouts.app')

@section('content')
<style>

    .rating {
        direction: rtl;
        unicode-bidi: bidi-override;
        color: #ddd; /* Personal choice */
        font-size: 8px;
        margin-left: -15px;
    }
    .rating input {
        display: none;
    }
    .rating label:hover,
    .rating label:hover ~ label,
    .rating input:checked + label,
    .rating input:checked + label ~ label {
        color: #ffc107; /* Personal color choice. Lifted from Bootstrap 4 */
        font-size: 8px;
    }


    .front-stars, .back-stars, .star-rating {
        display: flex;
      }

      .star-rating {
        align-items: left;
        font-size: 1.5em;
        justify-content: left;
        margin-left: -5px;
      }

      .back-stars {
        color: #CCC;
        position: relative;
      }

      .front-stars {
        color: #FFBC0B;
        overflow: hidden;
        position: absolute;
        top: 0;
        transition: all 0.5s;
      }


      .percent {
        color: #bb5252;
        font-size: 1.5em;
      }
    </style>
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('frontend.homepage')}}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('frontend.shop')}}">Cửa hàng</a></li>
                    <li class="breadcrumb-item">{{$product->title}}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                @include('frontend.account.common.message')
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner bg-light">

                            @if ($product->product_images)
                                @foreach ($product->product_images as $key => $productImage)
                                    <div class="carousel-item {{ ($key == 0) ? 'active' : ''}}">
                                        <img class="w-100 h-100" src="{{asset('uploads/product/large/'.$productImage->image)}}" alt="Image">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a>
                    </div>
                </div>


                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{$product->title}}</h1>
                        <div class="d-flex mb-3">
                            <div class="star-rating mt-2" title="">
                                <div class="back-stars">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>

                                    <div class="front-stars" style="width: {{ $avgRatingPer }}%">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2 ps-2">({{ ($product->product_ratings_count > 1) ? $product->product_ratings_count.' Reviews' : $product->product_ratings_count.' Review'}})</div>
                        </div>
                        @if ($product->compare_price > 0)
                            <h2 class="price text-secondary"><del>{{number_format($product->compare_price)}}đ</del></h2>
                        @endif
                        <h2 class="price ">{{number_format($product->price)}}đ</h2>

                        {!! $product->short_description !!}

                        @if ($product->track_qty == 'Yes')
                            @if($product->qty > 0)
                                <a href="javascript:void(0);" onclick="addToCart({{ $product->id}});" class="btn btn-dark"><i class="fas fa-shopping-cart"></i> &nbsp;Thêm vào giỏ hàng</a>
                            </a>
                            @else
                            <a class="btn btn-dark" href="javascript:void(0);">
                                <i class="fa fa-shopping-cart"></i> Hết hàng
                            </a>
                            @endif
                        @else
                            <a href="javascript:void(0);" onclick="addToCart({{ $product->id}});" class="btn btn-dark"><i class="fas fa-shopping-cart"></i> &nbsp;Thêm vào giỏ hàng</a>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Mô tả</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Thông số kỹ thuật</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Đánh giá</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                {!! $product->description !!}
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                {!! $product->shipping_returns !!}
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="col-md-8">
                                    <div class="row">
                                        <form action="" method="post" name="productRatingForm" id="productRatingForm">
                                            <h3 class="h4 pb-3">Viết đánh giá</h3>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="name">Tên</label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Tên">
                                                <p></p>
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                                                <p></p>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="rating">Đánh giá</label>
                                                <br>
                                                <div class="rating" style="width: 10rem">
                                                    <input id="rating-5" type="radio" name="rating" value="5"/><label for="rating-5"><i class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-4" type="radio" name="rating" value="4"  /><label for="rating-4"><i class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-3" type="radio" name="rating" value="3"/><label for="rating-3"><i class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-2" type="radio" name="rating" value="2"/><label for="rating-2"><i class="fas fa-3x fa-star"></i></label>
                                                    <input id="rating-1" type="radio" name="rating" value="1"/><label for="rating-1"><i class="fas fa-3x fa-star"></i></label>
                                                </div>
                                                <p class="product-rating-error text-danger"></p>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="">Trải nghiệm của bạn thế nào?</label>
                                                <textarea name="comment"  id="comment" class="form-control" cols="30" rows="10" placeholder="Trải nghiệm của bạn thế nào?"></textarea>
                                                <p></p>
                                            </div>
                                            <div>
                                                <button class="btn btn-dark">Gửi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <div class="overall-rating mb-3">
                                        <div class="d-flex">
                                            <h1 class="h3 pe-3"> {{ $avgRating }}</h1>
                                            <div class="star-rating mt-2" title="">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" style="width: {{ $avgRatingPer }}%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pt-2 ps-2">({{ ($product->product_ratings_count > 1) ? $product->product_ratings_count.' Reviews' : $product->product_ratings_count.' Review'}})</div>
                                        </div>

                                    </div>
                                    @if($product->product_ratings->isNotEmpty())
                                    @foreach ($product->product_ratings as $rating)
                                    @php
                                        $ratingPer = ($rating->rating*100)/5;
                                    @endphp
                                    <div class="rating-group mb-4">
                                       <span> <strong>{{ $rating->username}} </strong></span>
                                        <div class="star-rating mt-2" title="">
                                            <div class="back-stars">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>

                                                <div class="front-stars" style="width: {{ $ratingPer }}%">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-3">
                                            <p>{{ $rating->comment }}</p>
                                        </div>
                                        </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-5 section-8">
        <div class="container">
            <div class="section-title">
                <h2>Các sản phẩm liên quan</h2>
            </div>
            <div class="col-md-12">
                <div id="related-products" class="carousel">
                    @if (!empty($relatedProducts))
                        @foreach ($relatedProducts as $relProduct)
                        @php
                            $productImage = $relProduct->product_images->first();
                        @endphp
                        <div class="card product-card">
                            <div class="product-image position-relative">
                                <a href="{{ route("frontend.product",$product->slug)}}" class="product-img">
                                    @if (!empty($productImage->image))
                                        <img src="{{ asset('/uploads/product/small/'.$productImage->image)}}" class="card-img-top" ></td>
                                    @else
                                        <img src="{{ asset('admin-assets/img/default-150x150.png')}}" class="card-img-top " ></td>
                                    @endif
                                </a>
                                <a onclick="addToWishlist( {{$product->id}} )" class="whishlist" href="javascript:void(0);"><i class="far fa-heart"></i></a>

                                <div class="product-action">
                                    @if ($relProduct->track_qty == 'Yes')
                                            @if($relProduct->qty > 0)
                                            <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{$relProduct->id}});">
                                                <i class="fa fa-shopping-cart"></i> Thêm vào giỏ hàng
                                            </a>
                                            @else
                                            <a class="btn btn-dark" href="javascript:void(0);">
                                                <i class="fa fa-shopping-cart"></i> Hết hàng
                                            </a>
                                            @endif
                                        @else
                                        <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{$relProduct->id}});">
                                            <i class="fa fa-shopping-cart"></i> Thêm vào giỏ hàng
                                        </a>
                                        @endif
                                </div>
                            </div>
                            <div class="card-body text-center mt-3">
                                <a class="h6 link" href="">{{$relProduct->title}}</a>
                                <div class="price mt-2">
                                    <span class="h5"><strong>{{number_format($relProduct->price)}}đ</strong></span>
                                    @if ($relProduct->compare_price > 0)
                                        <span class="h6 text-underline"><del>{{number_format($relProduct->compare_price)}}đ</del></span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('customJS')
<script>
    $("#productRatingForm").submit(function(event){
        event.preventDefault();
        $.ajax({
            url: '{{ route("frontend.saveRating", $product->id) }}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                var errors = response.errors;
                // $("button[type='submit']").prop('disabled', false);

                if (response.status == false) {
                    if (errors.name) {
                        $("#name").addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback').html(errors.name);
                    } else {
                        $("#name").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");
                    }

                    if (errors.email) {
                        $("#email").addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback').html(errors.email);
                    } else {
                        $("#email").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");
                    }

                    if (errors.comment) {
                        $("#comment").addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback').html(errors.comment);
                    } else {
                        $("#comment").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");
                    }

                    if (errors.rating) {
                        $(".product-rating-error").html(errors.rating);
                    } else {
                        $(".product-rating-error").html("");
                    }

                } else {
                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                    $("#email").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                    $("#comment").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                    $(".product-rating-error").html("");

                    window.location.href = "{{ route('frontend.product', $product->slug) }}";
                }
            }
        });
    });
</script>
@endsection

