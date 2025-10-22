# Coach Registration - Unified Form Implementation

## ✅ Successfully Updated!

Coach registration has been fully integrated into the main unified registration page at `http://127.0.0.1:8000/register`

## What Was Changed

### 1. Registration Form UI (`resources/views/auth/register.blade.php`)

#### Added Coach Radio Button Option
- New radio button card between "College" and "Sports Ambassador"
- Icon: People/team management SVG icon
- Label: **"Coach"** 
- Subtitle: "Manage teams & players"
- Value: `coach`

#### Added Coach-Specific Fields
Three new fields that only appear when "Coach" is selected:

1. **Phone Number** (Optional)
   - Input field with phone icon
   - Name: `phone`
   - Placeholder: "Phone number"

2. **Years of Experience** (Optional)
   - Number input with clock icon
   - Name: `experience_years`
   - Placeholder: "Years coaching"
   - Minimum value: 0

3. **Coaching Bio** (Optional)
   - Textarea with document icon
   - Name: `bio`
   - 3 rows tall
   - Placeholder: "Tell us about your coaching experience and qualifications..."

#### JavaScript Updates

Added `toggleCoachFields()` function:
- Shows/hides coach-specific fields based on selected user type
- Integrated into the form's dynamic field visibility system

Updated existing functions:
- `toggleAcademicFields()` - Now hides college/university fields for coaches
- `toggleSportField()` - Changes sport field name to `sport_id` for coaches
- `toggleClubField()` - Hides club selection for coaches
- `applyInitialState()` - Calls toggleCoachFields on page load
- Radio change listeners - Call toggleCoachFields when user type changes

### 2. Controller (`app/Http/Controllers/Auth/RegisteredUserController.php`)

#### Added Methods
- `createCoach()` - Returns coach registration view with sports list
- `storeCoach()` - Calls `store()` method with 'coach' type

#### Added Validation Rules
For `user_type === 'coach'`:
```php
'sport_id' => 'required|exists:sports,id',
'phone' => 'nullable|string|max:20',
'bio' => 'nullable|string|max:1000',
'experience_years' => 'nullable|integer|min:0',
```

#### Added Coach Creation Logic
```php
if ($userType === 'coach') {
    $coach = Coach::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->input('phone'),
        'sport_id' => $request->integer('sport_id'),
        'bio' => $request->input('bio'),
        'user_id' => $user->id,
        'socail_links' => [],
    ]);
    
    $user->update(['coach_id' => $coach->id]);
}
```

#### Added Import
- `use App\Models\Coach;`

### 3. Routes (`routes/auth.php`)

Added two routes:
- `GET /register/coach` → `createCoach()` - Named: `register.coach`
- `POST /register/coach` → `storeCoach()` - Named: `register.coach.store`

## How It Works

### User Flow

1. **Visit** `http://127.0.0.1:8000/register`

2. **See 6 User Type Options:**
   - Club
   - Player
   - Referee
   - College/University
   - **Coach** ⭐ (NEW)
   - Sports Ambassador

3. **Select "Coach"**
   - Club dropdown disappears (coaches don't select clubs at registration)
   - Sport dropdown stays visible and becomes required
   - Coach-specific fields appear:
     - Phone Number
     - Years of Experience
     - Coaching Bio

4. **Fill Required Fields:**
   - Choose user type: Coach ✓
   - Primary Sport: (select from dropdown) ✓
   - First Name ✓
   - Last Name ✓
   - Email Address ✓
   - Password ✓
   - Confirm Password ✓
   - Accept Terms ✓

5. **Fill Optional Fields:**
   - Phone Number
   - Years of Experience
   - Coaching Bio

6. **Submit Form**
   - Creates User account
   - Creates Coach profile
   - Assigns 'coach' role
   - Links records
   - Auto-login
   - Redirects to coach dashboard

## Field Visibility Logic

| User Type | Sport Field | Club Field | DOB | Guardian | College/Uni | Affiliation | Coach Fields |
|-----------|-------------|------------|-----|----------|-------------|-------------|--------------|
| Player | ✅ Required | ✅ Required | ✅ | Conditional | ❌ | ❌ | ❌ |
| Club | ✅ Required | ❌ (shows name input) | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Coach** | ✅ Required (as sport_id) | ❌ Hidden | ❌ | ❌ | ❌ | ❌ | ✅ Phone, Experience, Bio |
| Referee | ✅ Optional | ✅ Optional | ❌ | ❌ | ✅ | ✅ | ❌ |
| College | ❌ Hidden | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Ambassador | ✅ Optional | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |

## Technical Details

### Form Field Names
When coach is selected, these fields are submitted:
- `user_type` = "coach"
- `sport_id` = (selected sport ID) ← Note: Changed from 'sport' to 'sport_id'
- `first_name` = (text)
- `last_name` = (text)
- `email` = (email)
- `phone` = (text, optional)
- `experience_years` = (number, optional)
- `bio` = (text, optional)
- `password` = (password)
- `password_confirmation` = (password)

### Database Records Created
1. **users table:**
   - name = first_name + last_name
   - email
   - password (hashed)
   - coach_id = (ID of created coach)

2. **coaches table:**
   - first_name
   - last_name
   - email
   - phone
   - sport_id
   - bio
   - user_id
   - socail_links = []

3. **role_user table:**
   - user_id
   - role_id (coach role)

### Validation
- ✅ First Name: required, max 191 chars
- ✅ Last Name: required, max 191 chars
- ✅ Email: required, valid email, unique
- ✅ Sport ID: required, must exist in sports table
- ✅ Phone: optional, max 20 chars
- ✅ Bio: optional, max 1000 chars
- ✅ Experience Years: optional, integer, min 0
- ✅ Password: required, min 6 chars, must match confirmation

## Testing

### Quick Test Steps

1. Visit `http://127.0.0.1:8000/register`
2. Click the **"Coach"** radio button
3. Verify these fields are visible:
   - ✅ Primary Sport dropdown
   - ✅ First Name
   - ✅ Last Name
   - ✅ Email Address
   - ✅ Phone Number (coach-specific)
   - ✅ Years of Experience (coach-specific)
   - ✅ Coaching Bio (coach-specific)
   - ✅ Password fields
   - ✅ Terms checkbox

4. Verify these fields are HIDDEN:
   - ❌ Club selection dropdown
   - ❌ Date of Birth
   - ❌ Guardian fields
   - ❌ College/University
   - ❌ Affiliation

5. Fill in the form:
   - Select "Coach"
   - Choose a sport
   - Enter your name
   - Enter email (e.g., testcoach@example.com)
   - Optionally add phone, experience, bio
   - Set password
   - Check terms
   - Click "Sign Up"

6. You should be:
   - ✅ Logged in automatically
   - ✅ Redirected to `/coach-dashboard`
   - ✅ See your coach dashboard with your name

### Test Data Example
```
User Type: Coach
Sport: Soccer
First Name: John
Last Name: Smith
Email: johncoach@test.com
Phone: 555-123-4567
Experience: 10
Bio: Former professional player with 10 years coaching youth teams
Password: password123
```

## Summary

✅ **Coach option added to unified registration form**
✅ **Coach-specific fields show/hide dynamically**
✅ **JavaScript handles all field visibility logic**
✅ **Backend creates coach profile and assigns role**
✅ **Validation rules properly configured**
✅ **Auto-login and redirect to coach dashboard**
✅ **No linter errors**

The coach registration is now fully integrated into your main registration page and works seamlessly with the existing registration flow! 🎉

