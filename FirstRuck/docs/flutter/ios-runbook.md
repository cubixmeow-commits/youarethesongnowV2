# iOS development and testing runbook

## One-time local requirements

- Current Flutter stable SDK
- Current Xcode supported by that Flutter release
- Xcode command-line tools
- CocoaPods if the generated iOS project or future plugins require it
- At least one installed iOS Simulator runtime

Run environment checks before creating code:

```bash
flutter --version
flutter doctor -v
xcodebuild -version
flutter devices
```

Resolve Flutter/Xcode/iOS warnings that block Simulator builds. Android tooling warnings do not block this iOS-only first milestone.

## Create the app

From `FirstRuck/`, if `mobile/` does not already exist:

```bash
flutter create --platforms=ios --org com.youarethesongnow --project-name first_ruck mobile
```

Then verify in Xcode that the Runner target uses:

- Display name: `First Ruck`
- Bundle identifier: `com.youarethesongnow.firstruck`
- Deployment target: iOS 16 or the current Flutter stable minimum if higher

Simulator builds do not require an Apple Developer signing team.

## Daily checks

From `FirstRuck/mobile`:

```bash
dart format --set-exit-if-changed lib test
flutter analyze
flutter test
flutter devices
flutter run -d <simulator-device-id>
```

Use an explicit device identifier returned by `flutter devices`; do not guess a device name.

## Simulator review matrix

For Milestone 1, review at least:

- one small/short supported iPhone in portrait;
- one current standard-size iPhone in portrait;
- one landscape check;
- large accessibility text;
- Reduce Motion enabled;
- light/dark system settings, even though the first app theme may intentionally remain field-guide dark/paper.

Capture the welcome and first-question screens from the Simulator for owner review.

## Physical iPhone later

A real iPhone build requires:

- the owners' Apple ID in Xcode;
- a unique registered bundle identifier;
- an appropriate development team and automatic/manual signing choice;
- Developer Mode on the iPhone;
- trust/pairing between the Mac and iPhone.

Do not commit provisioning profiles, signing certificates, Apple credentials, or team-specific secrets.

## Common boundaries

- A launch-screen image is not the interactive welcome screen.
- Do not request location permission on launch. Ask only at the moment the user chooses approximate device location and keep manual area entry available.
- Do not add background-location modes for the onboarding prototype.
- Keep network security strict; the live endpoint already uses HTTPS.
- If CocoaPods or generated files fail, record the exact tool versions and first relevant error before changing project settings.
