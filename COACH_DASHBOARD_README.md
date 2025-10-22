# Coach Dashboard Implementation

This document describes the implementation of the Coach Dashboard feature, which mirrors the functionality of the Club Dashboard.

## Overview

The Coach Dashboard provides coaches with a dedicated interface to manage their teams, players, training sessions, and matches. It includes all the core features from the Club Dashboard, adapted for coaching workflows.

## Files Created

### 1. Layout
- **File**: `resources/views/layouts/coach-dashboard.blade.php`
- **Description**: Main layout file for the coach dashboard with sidebar navigation, header, and theme toggle
- **Features**:
  - Responsive sidebar with mobile support
  - Quick access menu items for Events, Teams, Players, Training Sessions, Match Analysis, Tournaments, and Settings
  - Theme switcher (light/dark mode)
  - Logout functionality

### 2. Controllers

#### DashboardController
- **File**: `app/Http/Controllers/Coach/DashboardController.php`
- **Methods**:
  - `index()`: Main dashboard view with statistics and metrics
  - `setup()`: Initial profile setup form
  - `storeSetup()`: Handles profile setup form submission
  - `composeDashboardData()`: Gathers all dashboard data (teams, players, matches, tournaments, etc.)
- **Features**:
  - Team and player statistics
  - Match scheduling and completion tracking
  - Injury reports and player transfers
  - Weather integration
  - Chat roster with players and other coaches
  - Calendar integration with events

#### ProfileController
- **File**: `app/Http/Controllers/Coach/ProfileController.php`
- **Methods**:
  - `edit()`: Show profile edit form
  - `update()`: Update coach profile with photo upload support
- **Features**:
  - Profile photo upload
  - Sport selection
  - Bio and contact information management

### 3. Views

#### Dashboard View
- **File**: `resources/views/coach/dashboard.blade.php`
- **Features**:
  - Weather widget with sport icon
  - Statistics cards (Teams, Players, Scheduled Matches, Completed Matches)
  - Performance metrics (Injuries, Transfers)
  - Quick Actions buttons
  - Calendar integration
  - Chat panel
  - Season stats

#### Setup View
- **File**: `resources/views/coach/setup.blade.php`
- **Description**: Initial profile setup form for new coaches
- **Fields**:
  - First Name
  - Last Name
  - Primary Sport
  - Phone Number
  - Bio

#### Profile Edit View
- **File**: `resources/views/coach/profile/edit.blade.php`
- **Description**: Profile management interface
- **Features**:
  - All setup fields plus photo upload
  - Preview of current photo
  - Form validation
  - Success/error notifications

### 4. Routes

#### File: `routes/web.php`
Added the following coach routes:
- `GET /coach-dashboard` → Main dashboard (named `coach-dashboard`)
- `GET /coach/setup` → Profile setup form
- `POST /coach/setup` → Store profile setup
- `GET /coach/dashboard` → Dashboard (alias)
- `GET /coach/profile` → Edit profile
- `PUT /coach/profile` → Update profile
- `GET /coach/events` → Events (placeholder)
- `GET /coach/teams` → Teams (placeholder)
- `GET /coach/players` → Players (placeholder)
- `GET /coach/training` → Training sessions (placeholder)
- `GET /coach/matches` → Match analysis (placeholder)
- `GET /coach/tournaments` → Tournaments (placeholder)

### 5. Authentication Updates

#### AuthenticatedSessionController
- **File**: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- **Change**: Added coach role check to redirect coaches to their dashboard after login

#### FrontendController
- **File**: `app/Http/Controllers/FrontendController.php`
- **Change**: Added coach role check to redirect coaches from the homepage

#### Redirect Route
- **File**: `routes/web.php`
- **Change**: Added coach case in the `/redirect` route

## Database Requirements

The implementation uses the existing `coaches` table with the following columns:
- `id`
- `first_name`
- `last_name`
- `email`
- `phone`
- `gender`
- `user_id` (foreign key to users table)
- `sport_id` (foreign key to sports table)
- `photo`
- `bio`
- `socail_links` (JSON)
- `city`
- `country_id`
- `age`
- `created_at`
- `updated_at`

The `users` table should have:
- `coach_id` column for linking users to coach profiles
- Role system via `role_user` pivot table

## How to Use

### 1. Creating a Coach User

To test the coach dashboard, you need a user with the 'coach' role:

```php
// In Laravel Tinker or a seeder
$user = User::create([
    'name' => 'John Doe',
    'email' => 'coach@example.com',
    'password' => bcrypt('password'),
]);

$user->assignRole('coach');
```

### 2. First Login

When a coach logs in for the first time:
1. They are redirected to `/coach/setup`
2. They fill in their profile information (name, sport, etc.)
3. After submission, they are redirected to the coach dashboard

### 3. Dashboard Features

Once set up, coaches can:
- View their teams and players
- Track scheduled and completed matches
- Monitor injury reports and transfers
- Access quick actions for common tasks
- View and manage calendar events
- Chat with players and other coaches
- Check tournament information
- View season statistics

### 4. Profile Management

Coaches can update their profile at any time:
1. Click "Settings" in the sidebar
2. Update their information
3. Upload a profile photo
4. Save changes

## Routes Summary

| Route | Method | Name | Description |
|-------|--------|------|-------------|
| `/coach-dashboard` | GET | `coach-dashboard` | Main dashboard |
| `/coach/setup` | GET | `coach.setup` | Profile setup form |
| `/coach/setup` | POST | `coach.setup.store` | Store profile setup |
| `/coach/profile` | GET | `coach.profile.edit` | Edit profile |
| `/coach/profile` | PUT | `coach.profile.update` | Update profile |
| `/coach/events` | GET | `coach.events.index` | Events list |
| `/coach/teams` | GET | `coach.teams.index` | Teams list |
| `/coach/players` | GET | `coach.players.index` | Players list |
| `/coach/training` | GET | `coach.training.index` | Training sessions |
| `/coach/matches` | GET | `coach.matches.index` | Match analysis |
| `/coach/tournaments` | GET | `coach.tournaments.index` | Tournaments |

## Implemented Inner Pages

The following features have been fully implemented:

### ✅ Events Management
- **Files**: `EventController.php`, `events/index.blade.php`, `events/create.blade.php`, `events/edit.blade.php`
- **Features**: Full CRUD for coach events, team assignment, event invitations
- **Routes**: index, create, store, edit, update, destroy

### ✅ Team Management  
- **Files**: `TeamController.php`, `teams/index.blade.php`, `teams/show.blade.php`
- **Features**: View all assigned teams, team details, player rosters
- **Routes**: index, show

### ✅ Player Management
- **Files**: `PlayerController.php`, `players/index.blade.php`, `players/show.blade.php`
- **Features**: View all players in coach's teams, detailed player profiles, statistics
- **Routes**: index, show

### 🔜 Coming Soon

The following features are placeholders and can be implemented next:

1. **Training Sessions**: Create and schedule training sessions
2. **Match Analysis**: Detailed match analytics and statistics  
3. **Tournaments**: Tournament registration and management

## Testing

To test the implementation:

1. Create a test coach user (as shown above)
2. Login with the coach credentials
3. Complete the profile setup
4. Explore the dashboard features
5. Test navigation between different sections
6. Try updating the profile

## Notes

- All routes are protected by authentication middleware
- Coach role check is enforced at the controller level
- The dashboard uses the same styling as the club dashboard for consistency
- Weather service integration is already included
- Chat functionality mirrors the club dashboard chat system
- Calendar uses FullCalendar.js library
- Charts use Chart.js library

## Dependencies

- Laravel (existing installation)
- Bootstrap 5.3.8
- Font Awesome 6.4.0
- FullCalendar 6.1.19
- Chart.js (latest)
- Existing models: Coach, Team, Player, Sport, Event, Tournament, etc.

## Support

For any issues or questions regarding the coach dashboard implementation, please refer to the club dashboard implementation as it follows the same patterns and structure.

