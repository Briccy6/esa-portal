@extends('layouts.public')

@section('title', 'Choose Registration Type - ' . config('app.name'))

@section('content')
<section class="registration-choice">
    <div class="registration-choice__inner">
        <header class="registration-choice__header">
            <p class="registration-choice__eyebrow">Start your ESA journey</p>
            <h1>Choose how you would like to join</h1>
            <p>Select the account that best fits your needs. You can review your details before completing registration.</p>
        </header>

        <div class="registration-choice__cards">
            <article class="registration-choice__card">
                <div class="registration-choice__card-head">
                    <span class="registration-choice__icon" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span>
                    <h2>Student registration</h2>
                </div>
                <div class="registration-choice__card-body">
                    <ul>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Join a course at one of our branches</span></li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Access learning materials and class resources</span></li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Take assessments and follow your progress</span></li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Keep your school learning in one place</span></li>
                    </ul>
                    <a class="registration-choice__action" href="{{ route('students.create') }}">
                        Register as a student <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </article>

            <article class="registration-choice__card">
                <div class="registration-choice__card-head">
                    <span class="registration-choice__icon" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                    <h2>Client registration</h2>
                </div>
                <div class="registration-choice__card-body">
                    <ul>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Explore premium resources and public assessments</span></li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Choose subscription plans and manage access</span></li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Purchase tokens for eligible practice tests</span></li>
                        <li><i class="fas fa-check-circle" aria-hidden="true"></i><span>Use the portal without school enrollment</span></li>
                    </ul>
                    <a class="registration-choice__action" href="{{ route('client.auth.register') }}">
                        Register as a client <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
        </div>

        <a class="registration-choice__home" href="{{ route('welcome') }}">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to the school homepage
        </a>
    </div>
</section>
@endsection
