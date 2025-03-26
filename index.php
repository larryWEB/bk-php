<?php
session_start();
 if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
 }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRAINKASH Quiz Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" id="app">
        <h1>BRAINKASH</h1>
        
        <!-- Welcome section (initially hidden) -->
        <div id="welcome-section" class="hidden">
            <div class="question-container">
                <h2><span id="greeting"></span>, <span id="user-name-display"></span>!</h2>
                <p>Choose a category for your quiz:</p>
                <div class="category-grid">
                    <div class="category-card" data-category="sport">Sports</div>
                    <div class="category-card" data-category="mathematics">Mathematics</div>
                    <div class="category-card" data-category="agriculture">Agriculture</div>
                    <div class="category-card" data-category="economic">Economics</div>
                    <div class="category-card" data-category="street">Street Happenings</div>
                    <div class="category-card" data-category="entertainment">Entertainment</div>
                    <div class="category-card" data-category="music">Music</div>
                    <div class="category-card" data-category="fashion">Fashion</div>
                </div>
            </div>
        </div>
        
        <!-- Category Selection Section -->
        <div id="category-selection-section" class="hidden">
            <div class="question-container">
                <h2>Select Questions from Categories</h2>
                <p>Choose how many questions you want from each category (total must be 20):</p>
                <div class="category-selection-grid">
                    <div class="input-group">
                        <label for="sport-count">Sports</label>
                        <input type="number" id="sport-count" data-category="sport" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="mathematics-count">Mathematics</label>
                        <input type="number" id="mathematics-count" data-category="mathematics" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="agriculture-count">Agriculture</label>
                        <input type="number" id="agriculture-count" data-category="agriculture" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="economic-count">Economics</label>
                        <input type="number" id="economic-count" data-category="economic" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="street-count">Street Happenings</label>
                        <input type="number" id="street-count" data-category="street" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="entertainment-count">Entertainment</label>
                        <input type="number" id="entertainment-count" data-category="entertainment" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="music-count">Music</label>
                        <input type="number" id="music-count" data-category="music" min="0" max="20" value="0" class="category-count">
                    </div>
                    <div class="input-group">
                        <label for="fashion-count">Fashion</label>
                        <input type="number" id="fashion-count" data-category="fashion" min="0" max="20" value="0" class="category-count">
                    </div>
                </div>
                <p>Total Questions: <span id="total-questions-count">0</span>/20</p>
                <div id="category-error" class="error"></div>
                <div class="btn-container">
                    <button id="back-to-welcome-btn">Back</button>
                    <button id="confirm-categories-btn">Continue</button>
                </div>
            </div>
        </div>
        
        <!-- Rules section (initially hidden) -->
        <div id="rules-section" class="hidden">
            <div class="question-container">
                <h2>Quiz Rules</h2>
                <div class="rules-list">
                    <p><strong>1.</strong> You have 300 seconds to answer all questions.</p>
                    <p><strong>2.</strong> You cannot go back to previous questions once you click next.</p>
                    <p><strong>3.</strong> No form of cheating is allowed.</p>
                </div>
                <p>You will answer 20 questions, each worth 5 points, for a total of 100 possible points.</p>
                <p>Your category: <strong><span id="selected-category-display"></span></strong></p>
                <div class="btn-container">
                    <button id="back-to-categories-btn">Back to Categories</button>
                    <button id="start-quiz-btn">Start Quiz</button>
                </div>
            </div>
        </div>
        
        <!-- Quiz section (initially hidden) -->
        <div id="quiz-section" class="hidden">
            <div class="timer-container">
                <div class="circular-timer">
                    <div class="timer-circle">
                        <div class="timer-circle-inner">
                            <span id="global-timer-display">300</span>
                        </div>
                    </div>
                    <div class="timer-label">Time Remaining</div>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress" id="progress-bar"></div>
            </div>
            <div class="question-container">
                <h2 id="question-text">Question text will appear here</h2>
                <div class="options-container" id="options-container">
                    <!-- Options will be inserted here -->
                </div>
                <div id="answer-feedback-text" class="feedback-text hidden"></div>
            </div>
            <div class="btn-container">
                <button id="next-btn">Next Question</button>
            </div>
        </div>
        
        <!-- Result section (initially hidden) -->
        <div id="result-section" class="hidden">
            <div class="result-container">
                <h2>Quiz Complete!</h2>
                <p class="score">Your score: <span id="score-display">0</span>/100</p>
                <div id="perfect-score-section" class="hidden">
                    <p>Congratulations on your perfect score! Please fill out the form below to register your achievement.</p>
                    <form id="user-info-form">
                        <div class="input-group">
                            <label for="full-name">Full Name</label>
                            <input type="text" id="full-name" name="full-name" required>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="input-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="input-group">
                            <label for="comment">Comments (Optional)</label>
                            <textarea id="comment" name="comment" rows="3"></textarea>
                        </div>
                        <input type="hidden" id="score-input" name="score">
                        <button type="submit">Submit</button>
                    </form>
                </div>
                <div id="try-again-section" class="hidden">
                    <p>Thank you for taking our quiz! We are still finalizing our platform.</p>
                    <p>Please provide your email so we can notify you when we launch officially:</p>
                    <div class="input-group">
                        <input type="email" id="contact-email" placeholder="Your email address">
                        <div class="error" id="email-error"></div>
                    </div>
                    <button id="contact-submit-btn">Submit</button>
                    <button id="try-again-btn" style="margin-top: 15px;">Try Again</button>
                </div>
            </div>
        </div>
        
        <!-- Name input modal (shown first) -->
        <div class="modal-overlay" id="name-modal">
            <div class="modal">
                <h2>Welcome to BRAINKASH</h2>
                <p>Please enter your name to begin:</p>
                <div class="input-group">
                    <input type="text" id="name-input" placeholder="Enter your name">
                    <div class="error" id="name-error"></div>
                </div>
                <button id="name-submit-btn">Continue</button>
            </div>
        </div>
        
        <!-- Thank you modal (hidden initially) -->
        <div class="modal-overlay hidden" id="thank-you-modal">
            <div class="modal">
                <h2>Thank You!</h2>
                <p>Your information has been submitted successfully.</p>
                <button id="close-thank-you-btn">Close</button>
            </div>
        </div>
    </div>

    <!-- JavaScript files -->
    <script src="js/main.js"></script>
    <script src="js/quiz-timer.js"></script>
    <script src="js/result-report.js"></script>
    <script src="js/questions/agric.js"></script>
    <script src="js/questions/fashion.js"></script>
    <script src="js/questions/street.js"></script>
    <script src="js/questions/sport.js"></script>
    <script src="js/questions/mathematics.js"></script>
    <script src="js/questions/economic.js"></script>
    <script src="js/questions/entertainment.js"></script>
    <script src="js/questions/music.js"></script>
  
 
  
 

    <!-- Required libraries for PDF and DOC generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</body>
</html>
