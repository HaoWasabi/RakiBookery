<!-- "Giới thiệu - MeepBookery"; -->
<?php $foundedYear = 2024; ?>
<!-- About Us Banner -->
<section class="about-banner py-2 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="about-banner-content text-center">
                    <h1 class="section-title mb-4">Về chúng tôi</h1>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="our-story py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="about-image position-relative">
                    <img src="../../img/about/about-1.jpg" alt="Our Story" class="img-fluid rounded shadow">
                    <div
                        class="experience-badge bg-danger text-white shadow py-3 px-4 position-absolute bottom-0 end-0 translate-middle-y rounded-pill">
                        <h3 class="mb-0">+<?= date("Y") - $foundedYear ?></h3>
                        <p class="mb-0">Năm kinh nghiệm</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="about-content ps-lg-4">
                    <h6 class="text-danger fw-bold">CÂU CHUYỆN CỦA CHÚNG TÔI</h6>
                    <h2 class="mb-4">MeepBookery - Hành trình của đam mê sách</h2>
                    <p class="lead">MeepBookery được thành lập vào năm <?= $foundedYear ?>, với khởi đầu khiêm tốn là
                        một hiệu sách nhỏ
                        tại trung tâm thành phố Hồ Chí Minh.</p>
                    <p>Từ một cửa hàng nhỏ với vài trăm đầu sách, MeepBookery đã phát triển mạnh mẽ và trở thành một
                        trong những hiệu sách trực tuyến lớn nhất Việt Nam, với hơn 20.000 đầu sách đa dạng từ nhiều thể
                        loại khác nhau.</p>
                    <p>Chúng tôi tự hào mang đến cho độc giả những tác phẩm chất lượng từ các tác giả nổi tiếng trong
                        nước và quốc tế, với mục tiêu nuôi dưỡng tình yêu đọc sách cho mọi lứa tuổi.</p>
                    <div class="row mt-4">
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center about-feature">
                                <div class="icon-box bg-danger text-white rounded-circle me-3"
                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">20.000+ Đầu sách</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center about-feature">
                                <div class="icon-box bg-danger text-white rounded-circle me-3"
                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">50.000+ Khách hàng</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center about-feature">
                                <div class="icon-box bg-danger text-white rounded-circle me-3"
                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Giao hàng toàn quốc</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center about-feature">
                                <div class="icon-box bg-danger text-white rounded-circle me-3"
                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Chất lượng đảm bảo</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission -->
<section class="our-mission py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h6 class="text-danger fw-bold">SỨ MỆNH CỦA CHÚNG TÔI</h6>
                <h2 class="section-title">Nâng cao văn hoá đọc tại Việt Nam</h2>
                <div class="separator mx-auto"></div>
                <p class="lead mt-4">Chúng tôi cam kết mang đến trải nghiệm đọc sách tuyệt vời với giá cả phải chăng,
                    đồng thời thúc đẩy một nền văn hoá đọc sách mạnh mẽ.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="mission-card text-center p-4 bg-white rounded shadow h-100">
                    <div class="mission-icon mb-3">
                        <i class="fas fa-hand-holding-heart fa-3x text-danger"></i>
                    </div>
                    <h3>Trách nhiệm</h3>
                    <p>Chúng tôi cam kết mang đến những cuốn sách chất lượng, đa dạng về nội dung và thể loại, đáp ứng
                        nhu cầu của mọi độc giả.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="mission-card text-center p-4 bg-white rounded shadow h-100">
                    <div class="mission-icon mb-3">
                        <i class="fas fa-lightbulb fa-3x text-danger"></i>
                    </div>
                    <h3>Sáng tạo</h3>
                    <p>Chúng tôi không ngừng đổi mới, sáng tạo trong việc giới thiệu sách, tổ chức các sự kiện văn hoá
                        và kết nối cộng đồng yêu sách.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="mission-card text-center p-4 bg-white rounded shadow h-100">
                    <div class="mission-icon mb-3">
                        <i class="fas fa-star fa-3x text-danger"></i>
                    </div>
                    <h3>Chất lượng</h3>
                    <p>Chúng tôi đặt chất lượng lên hàng đầu, từ việc tuyển chọn sách, dịch vụ khách hàng đến quy trình
                        đóng gói và giao hàng.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h6 class="text-danger fw-bold">ĐỘI NGŨ CỦA CHÚNG TÔI</h6>
                <h2 class="section-title">Gặp gỡ những người đứng sau MeepBookery</h2>
                <div class="separator mx-auto"></div>
                <p class="lead mt-4">Đội ngũ tâm huyết của chúng tôi với niềm đam mê sách và cam kết mang đến trải
                    nghiệm tốt nhất cho khách hàng.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-1 d-lg-block d-none"></div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="team-member text-center">
                    <div class="team-image mb-3">
                        <img src="../../img/about/team-1.jpg" alt="Nguyen Van A" class="img-fluid rounded-circle shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4 class="mb-1">HTPTHANH</h4>
                    <p class="text-muted mb-2">Nhà sáng lập & CEO</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="team-member text-center">
                    <div class="team-image mb-3">
                        <img src="../../img/about/team-2.jpg" alt="Tran Thi B" class="img-fluid rounded-circle shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4 class="mb-1">TGHAO</h4>
                    <p class="text-muted mb-2">Giám đốc Marketing</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="team-member text-center">
                    <div class="team-image mb-3">
                        <img src="../../img/about/team-3.jpg" alt="Le Van C" class="img-fluid rounded-circle shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4 class="mb-1">LHMIH</h4>
                    <p class="text-muted mb-2">Giám đốc Sản phẩm</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="team-member text-center">
                    <div class="team-image mb-3">
                        <img src="../../img/about/team-4.jpg" alt="Pham Thi D" class="img-fluid rounded-circle shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4 class="mb-1">LHMN</h4>
                    <p class="text-muted mb-2">Trưởng phòng Kinh doanh</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="team-member text-center">
                    <div class="team-image mb-3">
                        <img src="../../img/about/team-5.jpg" alt="Hoang Van E" class="img-fluid rounded-circle shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4 class="mb-1">Nael Tuhline</h4>
                    <p class="text-muted mb-2">Trưởng phòng CSKH</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 d-lg-block d-none"></div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h6 class="text-danger fw-bold">ĐÁNH GIÁ TỪ KHÁCH HÀNG</h6>
                <h2 class="section-title">Khách hàng nói gì về chúng tôi</h2>
                <div class="separator mx-auto"></div>
                <p class="lead mt-4">Niềm tin và sự hài lòng của khách hàng là động lực để chúng tôi không ngừng phát
                    triển và hoàn thiện mỗi ngày.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card p-4 bg-white rounded shadow h-100">
                    <div class="testimonial-content mb-3">
                        <div class="rating text-warning mb-3">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Tôi đã trở thành khách hàng thân thiết của MeepBookery được hơn 2
                            năm. Tôi rất ấn tượng với sự đa dạng về đầu sách và chất lượng dịch vụ khách hàng tại đây.
                            Mỗi đơn hàng đều được đóng gói cẩn thận và giao đến tận tay một cách nhanh chóng."</p>
                    </div>
                    <div class="testimonial-author d-flex align-items-center">
                        <img src="../../img/about/testimonial-1.jpg" alt="Nguyen Thi Ngoc"
                            class="img-fluid rounded-circle me-3" width="60">
                        <div>
                            <h5 class="mb-1">Nguyễn Thị Ngọc</h5>
                            <p class="text-muted mb-0">Giáo viên, TP.HCM</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card p-4 bg-white rounded shadow h-100">
                    <div class="testimonial-content mb-3">
                        <div class="rating text-warning mb-3">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"MeepBookery là thiên đường cho những người yêu sách như tôi. Tôi
                            thích cách họ sắp xếp và giới thiệu sách theo từng chủ đề, giúp tôi dễ dàng tìm kiếm những
                            cuốn sách phù hợp. Chương trình khách hàng thân thiết cũng rất hấp dẫn với nhiều ưu đãi."
                        </p>
                    </div>
                    <div class="testimonial-author d-flex align-items-center">
                        <img src="../../img/about/testimonial-2.jpg" alt="Tran Minh Duc"
                            class="img-fluid rounded-circle me-3" width="60">
                        <div>
                            <h5 class="mb-1">Trần Minh Đức</h5>
                            <p class="text-muted mb-0">Kỹ sư phần mềm, Hà Nội</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card p-4 bg-white rounded shadow h-100">
                    <div class="testimonial-content mb-3">
                        <div class="rating text-warning mb-3">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">"Là một phụ huynh, tôi rất quan tâm đến việc tìm kiếm sách hay cho
                            con. MeepBookery có một bộ sưu tập sách thiếu nhi phong phú với nhiều tựa sách chất lượng.
                            Tôi đặc biệt thích các sự kiện định kỳ dành cho trẻ em mà họ tổ chức tại cửa hàng."</p>
                    </div>
                    <div class="testimonial-author d-flex align-items-center">
                        <img src="../../img/about/testimonial-3.png" alt="Le Thanh Huyen"
                            class="img-fluid rounded-circle me-3" width="60">
                        <div>
                            <h5 class="mb-1">Lê Thanh Huyền</h5>
                            <p class="text-muted mb-0">Nhân viên văn phòng, Đà Nẵng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners -->
<section class="partners py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h6 class="text-danger fw-bold">ĐỐI TÁC CỦA CHÚNG TÔI</h6>
                <h2 class="section-title">Hợp tác để phát triển</h2>
                <div class="separator mx-auto"></div>
                <p class="lead mt-4">Chúng tôi tự hào hợp tác với các nhà xuất bản và đối tác uy tín để mang đến những
                    sản phẩm chất lượng nhất.</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row" data-aos="fade-up">
                    <div class="col-4 col-md-2 mb-4 text-center">
                        <img src="../../img/about/partner-1.jpg" alt="FHS" class="img-fluid partner-logo"
                            style="max-height: 80px;">
                    </div>
                    <div class="col-4 col-md-2 mb-4 text-center">
                        <img src="../../img/about/partner-2.jpg" alt="NXBKD" class="img-fluid partner-logo"
                            style="max-height: 80px;">
                    </div>
                    <div class="col-4 col-md-2 mb-4 text-center">
                        <img src="../../img/about/partner-3.jpg" alt="NN" class="img-fluid partner-logo"
                            style="max-height: 80px;">
                    </div>
                    <div class="col-4 col-md-2 mb-4 text-center">
                        <img src="../../img/about/partner-4.jpg" alt="Alphabooks" class="img-fluid partner-logo"
                            style="max-height: 80px;">
                    </div>
                    <div class="col-4 col-md-2 mb-4 text-center">
                        <img src="../../img/about/partner-5.jpg" alt="FN" class="img-fluid partner-logo"
                            style="max-height: 80px;">
                    </div>
                    <div class="col-4 col-md-2 mb-4 text-center">
                        <img src="../../img/about/partner-6.jpg" alt="PN" class="img-fluid partner-logo"
                            style="max-height: 80px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5 bg-danger text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0" data-aos="fade-right">
                <h2 class="mb-3">Khám phá kho tàng sách tại MeepBookery ngay hôm nay</h2>
                <p class="lead mb-0">Với hơn 20.000 đầu sách đa dạng thể loại, chúng tôi cam kết mang đến cho bạn những
                    trải nghiệm đọc sách tuyệt vời nhất.</p>
            </div>
            <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                <a href="index.php" class="btn btn-outline-light btn-lg px-4 py-3">
                    <i class="fas fa-book-open me-2"></i> Khám phá ngay
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section py-3 bg-danger text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h3 class="mb-1">Đăng ký nhận thông tin</h3>
                <p class="mb-0">Hãy đăng ký để nhận thông tin về sách mới và ưu đãi từ MeepBookery.</p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0" data-aos="fade-left">
                <form class="newsletter-form d-flex">
                    <input type="email" class="form-control me-2" placeholder="Nhập email của bạn" required>
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-paper-plane me-1"></i> Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>