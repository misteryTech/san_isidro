<?php
session_start();
ob_start(); // Capture page content
?>

<section class="section">
    <div class="row justify-content-center">

        <!-- Membership Upgrade Announcement -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h3 class="card-title fw-bold">Upgrade to Regular Membership</h3>
                    <p class="mb-3">
                        As part of our community growth, we are now offering all Associate Members
                        the opportunity to upgrade into <strong>Regular Members</strong>.
                    </p>

                    <div class="alert alert-primary">
                        Enjoy more benefits, full voting rights, and exclusive member privileges.
                    </div>

                    <a href="member_upgrade.php" class="btn btn-primary btn-lg mt-2">
                        Upgrade Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Carousel Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Announcements Carousel</h5>
                    <p class="text-muted">
                        Stay updated with the latest events, programs, and announcements.
                    </p>

                    <!-- Carousel with fade transition -->
                    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="../assets/img/slides-1.jpg" class="d-block w-100" alt="Slide 1">
                            </div>
                            <div class="carousel-item">
                                <img src="../assets/img/slides-2.jpg" class="d-block w-100" alt="Slide 2">
                            </div>
                            <div class="carousel-item">
                                <img src="../assets/img/slides-3.jpg" class="d-block w-100" alt="Slide 3">
                            </div>
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    <!-- End Carousel -->
                </div>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
