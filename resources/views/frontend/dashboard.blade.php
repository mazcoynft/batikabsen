<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - USSIBATIK ABSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend-mobile.css') }}">
    <style>
        /* Dashboard Specific Styles */
        
        /* Quick Menu Styles */
        .quick-menu-container {
            margin-top: 16px;
            margin-bottom: 16px;
        }
        
        .quick-menu-grid {
            display: flex;
            justify-content: space-around;
            gap: 12px;
        }
        
        .quick-menu-link {
            flex: 1;
            text-decoration: none;
            outline: none !important;
        }
        
        .quick-menu-link:focus {
            outline: none !important;
        }
        
        .quick-menu-card {
            background: white;
            border-radius: 16px;
            padding: 20px 12px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid transparent;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .quick-menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .quick-menu-card:hover::before {
            left: 100%;
        }
        
        .quick-menu-card:hover {
            transform: translateY(-12px) scale(1.08);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: rgba(0, 102, 255, 0.5);
        }
        
        .quick-menu-icon {
            font-size: 28px;
            margin-bottom: 12px;
            display: block;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            z-index: 1;
        }
        
        .quick-menu-card:hover .quick-menu-icon {
            transform: scale(1.3) rotate(10deg);
            animation: iconPulse 1.5s infinite;
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: scale(1.3) rotate(10deg); }
            50% { transform: scale(1.4) rotate(15deg); }
        }
        
        .quick-menu-label {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .quick-menu-card:hover .quick-menu-label {
            color: #0066ff;
            transform: translateY(-2px);
        }
        
        /* Header Text Alignment Fix */
        .header .user-profile {
            justify-content: flex-start;
        }
        
        .header .user-info {
            text-align: left !important;
        }
        
        .header .user-name,
        .header .user-position {
            text-align: left !important;
        }
        
        .attendance-card {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 50%, #00aaff 100%);
            color: white;
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0, 102, 255, 0.5);
            position: relative;
            overflow: hidden;
            animation: cardFloat 6s ease-in-out infinite;
        }
        
        @keyframes cardFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .attendance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 8s linear infinite;
        }
        
        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .attendance-title {
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .attendance-stats {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 2;
        }
        
        .stat-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 12px 6px;
            flex: 1;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }
        
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        
        .stat-item:hover::before {
            left: 100%;
        }
        
        .stat-item:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.25);
        }
        
        .stat-value {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 2px;
            line-height: 1;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            animation: numberPulse 2s ease-in-out infinite;
        }
        
        @keyframes numberPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .stat-label {
            font-size: 9px;
            opacity: 0.95;
            font-weight: 600;
            line-height: 1.1;
            position: relative;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .check-time {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 16px;
            margin-top: 12px;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .check-date {
            text-align: center;
            font-weight: 700;
            color: #333;
            margin-bottom: 12px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .check-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        
        .check-in, .check-out {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 16px 12px;
            border-radius: 16px;
            min-height: 70px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .check-in {
            background: linear-gradient(135deg, #00c851 0%, #007e33 100%);
            box-shadow: 0 4px 20px rgba(0, 200, 81, 0.3);
        }
        
        .check-out {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            box-shadow: 0 4px 20px rgba(255, 68, 68, 0.3);
        }
        
        .check-in::before, .check-out::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        
        .check-in:hover::before, .check-out:hover::before {
            left: 100%;
        }
        
        .check-in:hover, .check-out:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }
        
        .check-in:hover {
            box-shadow: 0 12px 32px rgba(0, 200, 81, 0.4);
        }
        
        .check-out:hover {
            box-shadow: 0 12px 32px rgba(255, 68, 68, 0.4);
        }
        
        .attendance-photo, .attendance-photo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            background: rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .attendance-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .attendance-photo:hover img {
            transform: scale(1.1);
        }
        
        .attendance-photo-placeholder {
            animation: photoPlaceholderPulse 2s ease-in-out infinite;
        }
        
        @keyframes photoPlaceholderPulse {
            0%, 100% { 
                background: rgba(255, 255, 255, 0.2);
                transform: scale(1);
            }
            50% { 
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.05);
            }
        }
        
        .attendance-photo-placeholder i {
            font-size: 1.3rem;
            color: white;
            animation: cameraIcon 3s ease-in-out infinite;
        }
        
        @keyframes cameraIcon {
            0%, 100% { transform: scale(1) rotate(0deg); }
            25% { transform: scale(1.1) rotate(-5deg); }
            75% { transform: scale(1.1) rotate(5deg); }
        }
        
        .check-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .check-time-value {
            font-size: 1.1rem;
            font-weight: 800;
            color: white;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            animation: timeGlow 3s ease-in-out infinite;
        }
        
        @keyframes timeGlow {
            0%, 100% { text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
            50% { text-shadow: 0 2px 8px rgba(255,255,255,0.5), 0 2px 4px rgba(0,0,0,0.3); }
        }
        
        .check-status {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            margin-top: 2px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Announcement Card */
        .announcement-card {
            position: relative;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #8b5cf6 100%);
            color: #fff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
            animation: fadeIn 0.4s ease-out;
        }
        
        .announcement-card::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle at center, rgba(255,255,255,0.25), transparent 60%);
            transform: rotate(25deg);
        }
        
        .announcement-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            color: #fff;
            position: relative;
            z-index: 1;
        }
        
        .announcement-title i {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 8px;
            margin-right: 10px;
            color: #fff;
        }
        
        .announcement-item {
            padding: 12px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }
        
        .announcement-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.2);
        }
        
        .announcement-type {
            font-weight: 600;
            color: #fff;
            opacity: 0.95;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .announcement-content {
            color: rgba(255,255,255,0.95);
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Tab Navigation */
        .nav-tabs {
            border-bottom: none;
            margin-bottom: 16px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        
        .nav-tabs .nav-item {
            flex: 1;
            max-width: 200px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 12px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }
        
        .nav-tabs .nav-link:hover {
            color: #0066ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .nav-tabs .nav-link.active {
            color: white;
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.4);
        }
        
        /* Attendance List */
        .attendance-list-card {
            padding: 16px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .attendance-item, .leaderboard-item {
            padding: 14px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            background-color: white;
            margin-bottom: 12px;
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }
        
        .attendance-item:hover, .leaderboard-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            border-color: #0066ff;
        }
        
        .attendance-date {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .attendance-time {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }
        
        .attendance-status {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        /* Status Badges */
        .status-ontime, .status-late, .status-cuti, .status-izin, .status-sakit {
            display: inline-block;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-ontime {
            background-color: #28a745;
            color: white;
        }
        
        .status-late {
            background-color: #dc3545;
            color: white;
        }
        
        .status-cuti {
            background-color: #00aaff;
            color: white;
        }
        
        .status-izin {
            background-color: #ff9800;
            color: white;
        }
        
        .status-sakit {
            background-color: #009688;
            color: white;
        }
        
        .wfh-indicator, .onsite-indicator {
            display: inline-block;
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .wfh-indicator {
            background-color: #555;
            color: white;
        }
        
        .onsite-indicator {
            background-color: #2196F3;
            color: white;
        }
        
        /* Leaderboard */
        .leaderboard-rank {
            width: 36px;
            height: 36px;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 12px;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        
        .leaderboard-info {
            flex-grow: 1;
        }
        
        .leaderboard-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .leaderboard-position {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }
        
        .leaderboard-time {
            font-weight: 600;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 8px;
            color: white;
        }
        
        .time-ontime {
            background-color: #28a745;
        }
        
        .time-late {
            background-color: #dc3545;
        }
        
        /* Empty State */
        .text-center.py-3 {
            color: #6c757d;
            font-size: 14px;
            padding: 24px !important;
        }
        
        /* Additional Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.6s ease-out;
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }
        
        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }
        
        /* Icon Specific Animations */
        .fa-clock {
            animation: clockTick 2s ease-in-out infinite;
        }
        
        @keyframes clockTick {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
        
        .fa-user-shield {
            animation: shieldGlow 3s ease-in-out infinite;
        }
        
        @keyframes shieldGlow {
            0%, 100% { 
                filter: drop-shadow(0 0 5px rgba(40, 167, 69, 0.5));
                transform: scale(1);
            }
            50% { 
                filter: drop-shadow(0 0 15px rgba(40, 167, 69, 0.8));
                transform: scale(1.05);
            }
        }
        
        .fa-file-invoice-dollar {
            animation: dollarSpin 4s ease-in-out infinite;
        }
        
        @keyframes dollarSpin {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        .fa-folder-open {
            animation: folderBounce 2s ease-in-out infinite;
        }
        
        @keyframes folderBounce {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        /* Additional Icon Animations */
        .fa-calendar-check {
            animation: calendarFlip 4s ease-in-out infinite;
        }
        
        @keyframes calendarFlip {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        .fa-check-circle {
            animation: checkPulse 2s ease-in-out infinite;
        }
        
        @keyframes checkPulse {
            0%, 100% { 
                transform: scale(1);
                filter: drop-shadow(0 0 3px rgba(0, 200, 81, 0.5));
            }
            50% { 
                transform: scale(1.2);
                filter: drop-shadow(0 0 8px rgba(0, 200, 81, 0.8));
            }
        }
        
        .fa-exclamation-circle {
            animation: warningBlink 3s ease-in-out infinite;
        }
        
        @keyframes warningBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        .fa-sign-in-alt, .fa-sign-out-alt {
            animation: signMove 2s ease-in-out infinite;
        }
        
        @keyframes signMove {
            0%, 100% { transform: translateX(0px); }
            50% { transform: translateX(3px); }
        }
        
        .fa-calendar-day {
            animation: dayGlow 4s ease-in-out infinite;
        }
        
        @keyframes dayGlow {
            0%, 100% { 
                filter: drop-shadow(0 0 5px rgba(102, 126, 234, 0.5));
            }
            50% { 
                filter: drop-shadow(0 0 15px rgba(102, 126, 234, 0.8));
            }
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .stat-value {
                font-size: 18px;
            }
            
            .stat-label {
                font-size: 8px;
            }
            
            .stat-item {
                padding: 10px 4px;
            }
            
            .attendance-stats {
                gap: 6px;
            }
            
            .check-time-value {
                font-size: 0.9rem;
            }
            
            .check-status {
                font-size: 0.7rem;
            }
            
            .announcement-title {
                font-size: 15px;
            }
            
            .announcement-item {
                padding: 10px;
            }
            
            .announcement-type, .announcement-content {
                font-size: 13px;
            }
            
            .attendance-date, .attendance-time, .leaderboard-name {
                font-size: 13px;
            }
            
            .leaderboard-time {
                font-size: 13px;
                padding: 5px 10px;
            }
        }
        
        @media (max-width: 375px) {
            .attendance-card {
                padding: 12px;
            }
            
            .stat-item {
                padding: 8px 4px;
            }
            
            .stat-value {
                font-size: 16px;
            }
            
            .stat-label {
                font-size: 7px;
            }
            
            .attendance-stats {
                gap: 4px;
            }
            
            .check-in, .check-out {
                padding: 10px;
            }
            
            .attendance-photo, .attendance-photo-placeholder {
                width: 32px;
                height: 32px;
            }
        }
        
        /* Enhanced Dashboard Animations */
        .quick-menu-card {
            position: relative;
            overflow: hidden;
        }
        
        .quick-menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
            z-index: 0;
        }
        
        .quick-menu-card:hover::before {
            left: 100%;
        }
        
        .quick-menu-card:hover {
            transform: translateY(-12px) scale(1.08) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
            border-color: rgba(74, 144, 226, 0.3) !important;
        }
        
        .quick-menu-icon {
            position: relative;
            z-index: 1;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .quick-menu-card:hover .quick-menu-icon {
            transform: scale(1.3) rotate(10deg);
            animation: iconPulse 1.5s infinite;
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: scale(1.3) rotate(10deg); }
            50% { transform: scale(1.4) rotate(15deg); }
        }
        
        /* Icon Specific Animations */
        .fa-clock {
            animation: clockTick 2s ease-in-out infinite;
        }
        
        @keyframes clockTick {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
        
        .fa-user-shield {
            animation: shieldGlow 3s ease-in-out infinite;
        }
        
        @keyframes shieldGlow {
            0%, 100% { 
                filter: drop-shadow(0 0 5px rgba(40, 167, 69, 0.5));
                transform: scale(1);
            }
            50% { 
                filter: drop-shadow(0 0 15px rgba(40, 167, 69, 0.8));
                transform: scale(1.05);
            }
        }
        
        .fa-file-invoice-dollar {
            animation: dollarSpin 4s ease-in-out infinite;
        }
        
        @keyframes dollarSpin {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        .fa-folder-open {
            animation: folderBounce 2s ease-in-out infinite;
        }
        
        @keyframes folderBounce {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        /* Enhanced Attendance Card */
        .attendance-card {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 50%, #00aaff 100%) !important;
            border-radius: 24px !important;
            position: relative;
            overflow: hidden;
            animation: cardFloat 6s ease-in-out infinite;
        }
        
        @keyframes cardFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .attendance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 8s linear infinite;
            z-index: 0;
        }
        
        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .attendance-title {
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .stat-item {
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
            z-index: 0;
        }
        
        .stat-item:hover::before {
            left: 100%;
        }
        
        .stat-item:hover {
            transform: translateY(-8px) scale(1.05) !important;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2) !important;
            background: rgba(255, 255, 255, 0.25) !important;
        }
        
        .stat-value {
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            animation: numberPulse 2s ease-in-out infinite;
        }
        
        @keyframes numberPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .stat-label {
            position: relative;
            z-index: 1;
        }
        
        /* Enhanced Check Time */
        .check-time {
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 20px !important;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .check-in, .check-out {
            position: relative;
            overflow: hidden;
            border-radius: 16px !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .check-in {
            background: linear-gradient(135deg, #00c851 0%, #007e33 100%) !important;
            box-shadow: 0 4px 20px rgba(0, 200, 81, 0.3);
        }
        
        .check-out {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%) !important;
            box-shadow: 0 4px 20px rgba(255, 68, 68, 0.3);
        }
        
        .check-in::before, .check-out::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
            z-index: 0;
        }
        
        .check-in:hover::before, .check-out:hover::before {
            left: 100%;
        }
        
        .check-in:hover, .check-out:hover {
            transform: translateY(-6px) scale(1.02);
        }
        
        .check-in:hover {
            box-shadow: 0 12px 32px rgba(0, 200, 81, 0.4);
        }
        
        .check-out:hover {
            box-shadow: 0 12px 32px rgba(255, 68, 68, 0.4);
        }
        
        /* Photo Animations */
        .attendance-photo-placeholder {
            animation: photoPlaceholderPulse 2s ease-in-out infinite;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
        }
        
        @keyframes photoPlaceholderPulse {
            0%, 100% { 
                background: rgba(255, 255, 255, 0.2);
                transform: scale(1);
            }
            50% { 
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.05);
            }
        }
        
        .attendance-photo-placeholder i {
            animation: cameraIcon 3s ease-in-out infinite;
        }
        
        @keyframes cameraIcon {
            0%, 100% { transform: scale(1) rotate(0deg); }
            25% { transform: scale(1.1) rotate(-5deg); }
            75% { transform: scale(1.1) rotate(5deg); }
        }
        
        .check-time-value {
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            animation: timeGlow 3s ease-in-out infinite;
        }
        
        @keyframes timeGlow {
            0%, 100% { text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
            50% { text-shadow: 0 2px 8px rgba(255,255,255,0.5), 0 2px 4px rgba(0,0,0,0.3); }
        }
        
        .check-info {
            position: relative;
            z-index: 1;
        }
        
        /* Page Load Animations */
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.6s ease-out;
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }
        
        /* Additional Icon Animations */
        .fa-calendar-check {
            animation: calendarFlip 4s ease-in-out infinite;
        }
        
        @keyframes calendarFlip {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        .fa-check-circle {
            animation: checkPulse 2s ease-in-out infinite;
        }
        
        @keyframes checkPulse {
            0%, 100% { 
                transform: scale(1);
                filter: drop-shadow(0 0 3px rgba(0, 200, 81, 0.5));
            }
            50% { 
                transform: scale(1.2);
                filter: drop-shadow(0 0 8px rgba(0, 200, 81, 0.8));
            }
        }
        
        .fa-exclamation-circle {
            animation: warningBlink 3s ease-in-out infinite;
        }
        
        @keyframes warningBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        .fa-sign-in-alt, .fa-sign-out-alt {
            animation: signMove 2s ease-in-out infinite;
        }
        
        @keyframes signMove {
            0%, 100% { transform: translateX(0px); }
            50% { transform: translateX(3px); }
        }
        
        .fa-calendar-day {
            animation: dayGlow 4s ease-in-out infinite;
        }
        
        @keyframes dayGlow {
            0%, 100% { 
                filter: drop-shadow(0 0 5px rgba(102, 126, 234, 0.5));
            }
            50% { 
                filter: drop-shadow(0 0 15px rgba(102, 126, 234, 0.8));
            }
        }
    </style>
</head>
<body>
    <div class="header" style="padding: 12px 16px;">
        <div class="user-profile">
            <img src="{{ Auth::user()->avatar_url ? Storage::url(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4a90e2&color=fff' }}" alt="Profile" class="user-avatar">
            <div class="user-info">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="user-position">{{ optional(Auth::user()->karyawan)->jabatan ?? 'User' }}</p>
            </div>
            <form action="{{ route('frontend.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Quick Menu Icons -->
    <div class="container quick-menu-container animate-fade-in-up animate-delay-1">
        <div class="quick-menu-grid">
            <a href="{{ route('frontend.lembur.index') }}" class="quick-menu-link">
                <div class="quick-menu-card animate-fade-in-up animate-delay-1">
                    <i class="fas fa-clock quick-menu-icon" style="color: #0066ff;"></i>
                    <div class="quick-menu-label">Lembur</div>
                </div>
            </a>
            <a href="{{ route('frontend.piket.index') }}" class="quick-menu-link">
                <div class="quick-menu-card animate-fade-in-up animate-delay-2">
                    <i class="fas fa-user-shield quick-menu-icon" style="color: #28a745;"></i>
                    <div class="quick-menu-label">Piket</div>
                </div>
            </a>
            <a href="{{ route('frontend.dokumen.index', ['tipe' => 'slip_gaji']) }}" class="quick-menu-link">
                <div class="quick-menu-card animate-fade-in-up animate-delay-3">
                    <i class="fas fa-file-invoice-dollar quick-menu-icon" style="color: #ff9800;"></i>
                    <div class="quick-menu-label">Slip</div>
                </div>
            </a>
            <a href="{{ route('frontend.dokumen.index', ['tipe' => 'dokumen']) }}" class="quick-menu-link">
                <div class="quick-menu-card animate-fade-in-up animate-delay-4">
                    <i class="fas fa-folder-open quick-menu-icon" style="color: #9c27b0;"></i>
                    <div class="quick-menu-label">Doc</div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="container content">
        <!-- Card Kehadiran -->
        <div class="attendance-card animate-fade-in-up animate-delay-2">
            <div class="attendance-title">
                <i class="fas fa-calendar-check" style="margin-right: 8px; animation: pulse 2s infinite;"></i>
                {{ date('F Y') }}
            </div>
            <div class="attendance-stats">
                <div class="stat-item animate-slide-in-left animate-delay-1">
                    <p class="stat-value">{{ $kehadiran ?? '0' }}</p>
                    <p class="stat-label">
                        <i class="fas fa-check-circle" style="color: #00c851; margin-right: 4px;"></i>
                        Kehadiran
                    </p>
                </div>
                <div class="stat-item animate-fade-in-up animate-delay-2">
                    <p class="stat-value">{{ $izin ?? '0' }}</p>
                    <p class="stat-label">
                        <i class="fas fa-exclamation-circle" style="color: #ff9800; margin-right: 4px;"></i>
                        Izin/Sakit
                    </p>
                </div>
                <div class="stat-item animate-slide-in-right animate-delay-3">
                    <p class="stat-value">{{ $terlambat ?? '0' }}</p>
                    <p class="stat-label">
                        <i class="fas fa-clock" style="color: #ff4444; margin-right: 4px;"></i>
                        Terlambat
                    </p>
                </div>
            </div>
            
            <div class="check-time animate-fade-in-up animate-delay-3">
                <div class="check-date">
                    <i class="fas fa-calendar-day" style="margin-right: 6px; color: #0066ff;"></i>
                    {{ date('d F Y') }}
                </div>
                <div class="check-row">
                    <div class="check-in animate-slide-in-left animate-delay-4">
                        @if(isset($foto_masuk) && $foto_masuk)
                            <div class="attendance-photo">
                                <img src="{{ Storage::url($foto_masuk) }}" alt="Foto Absensi">
                            </div>
                        @else
                            <div class="attendance-photo-placeholder">
                                <i class="fas fa-camera"></i>
                            </div>
                        @endif
                        <div class="check-info">
                            <div class="check-time-value">{{ isset($jam_masuk) ? substr($jam_masuk, 0, 5) : '--:--' }}</div>
                            <div class="check-status">
                                Masuk
                            </div>
                        </div>
                    </div>
                    <div class="check-out animate-slide-in-right animate-delay-4">
                        @if(isset($foto_pulang) && $foto_pulang)
                            <div class="attendance-photo">
                                <img src="{{ Storage::url($foto_pulang) }}" alt="Foto Absensi">
                            </div>
                        @else
                            <div class="attendance-photo-placeholder">
                                <i class="fas fa-camera"></i>
                            </div>
                        @endif
                        <div class="check-info">
                            <div class="check-time-value">{{ isset($jam_pulang) ? substr($jam_pulang, 0, 5) : '--:--' }}</div>
                            <div class="check-status">
                                Pulang
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pengumuman Carousel -->
        <div class="announcement-card">
            <div class="announcement-title">
                <i class="fas fa-bullhorn"></i> Pengumuman
            </div>
            <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
                <div class="carousel-inner">
                    @if(count($pengumuman) > 0)
                        @foreach($pengumuman as $key => $p)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <div class="announcement-item">
                                    <div class="announcement-type">{{ $p->jenis_pengumuman }}</div>
                                    <div class="announcement-content">{{ $p->isi_pengumuman }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="carousel-item active">
                            <div class="announcement-item">
                                <div class="announcement-content">Belum ada pengumuman saat ini.</div>
                            </div>
                        </div>
                    @endif
                </div>
                @if(count($pengumuman) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Tab Kehadiran dan Leaderboard -->
        <ul class="nav nav-tabs" id="attendanceTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="monthly" aria-selected="true">Bulan Ini</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="leaderboard-tab" data-bs-toggle="tab" data-bs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">Leaderboard</button>
            </li>
        </ul>
        
        <div class="tab-content" id="attendanceTabContent">
            <!-- Tab Kehadiran Bulan Ini -->
            <div class="tab-pane fade show active" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                <div class="attendance-list-card">
                    @if(count($presensi) > 0)
                        @foreach($presensi as $p)
                        <div class="attendance-item">
                            <div>
                                <div class="attendance-date">{{ \Carbon\Carbon::parse($p->tgl_presensi)->format('d F Y') }}</div>
                                <div class="attendance-time">{{ $p->jam_in ?? '--:--' }}</div>
                            </div>
                            <div class="attendance-status">
                                @if($p->status == 'c')
                                    <span class="status-cuti">Cuti</span>
                                @elseif($p->status == 'i')
                                    <span class="status-izin">Izin</span>
                                @elseif($p->status == 's')
                                    <span class="status-sakit">Sakit</span>
                                @else
                                    @if($p->status_presensi_in == '3')
                                        <span class="onsite-indicator">Onsite</span>
                                    @elseif($p->status_presensi_in == '4')
                                        <span class="wfh-indicator">WFH</span>
                                    @endif
                                    
                                    <span class="{{ in_array($p->status_presensi_in, ['1']) ? 'status-ontime' : 'status-late' }}">
                                        {{ in_array($p->status_presensi_in, ['1']) ? 'ONTIME' : 'LATE' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">Belum ada data absensi bulan ini</div>
                    @endif
                </div>
            </div>
            
            <!-- Tab Leaderboard -->
            <div class="tab-pane fade" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
                <div class="attendance-list-card">
                    @if(count($leaderboard) > 0)
                        @foreach($leaderboard as $key => $l)
                        <div class="leaderboard-item">
                            <div class="leaderboard-rank" style="background-color: {{ $key < 3 ? '#0066ff' : '#6c757d' }}">{{ $key + 1 }}</div>
                            <div class="leaderboard-info">
                                <div class="leaderboard-name">{{ $l->karyawan->nama ?? 'Karyawan' }}</div>
                                <div class="leaderboard-position">
                                    @if($l->status == 'c')
                                        <span class="status-cuti">Cuti</span>
                                    @elseif($l->status == 'i')
                                        <span class="status-izin">Izin</span>
                                    @elseif($l->status == 's')
                                        <span class="status-sakit">Sakit</span>
                                    @else
                                        @if($l->status_presensi_in == '3')
                                            <span class="onsite-indicator">Onsite</span>
                                        @elseif($l->status_presensi_in == '4')
                                            <span class="wfh-indicator">WFH</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="leaderboard-time {{ in_array($l->status_presensi_in, ['1']) ? 'time-ontime' : 'time-late' }}">
                                {{ $l->jam_in ?? '--:--' }}
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">Belum ada data absensi hari ini</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Navigation Bar -->
    <nav class="navbar navbar-expand navbar-light bottom-nav">
        <div class="container">
            <ul class="navbar-nav w-100">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('frontend.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.history') }}">
                        <i class="fas fa-file-alt"></i>
                        <span>History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.absen') }}">
                        <div class="circle-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.izin.index') }}">
                        <i class="fas fa-calendar"></i>
                        <span>Izin</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.profile') }}">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
    
    {{-- Mobile Optimizations --}}
    <script src="{{ asset('js/mobile-optimizations.js') }}"></script>
    
    {{-- Frontend Security --}}
    <script src="{{ asset('js/frontend-security.js') }}"></script>
</body>
</html>
