import 'package:flutter/material.dart';

import '../../../../app/first_ruck_colors.dart';
import '../../../../app/first_ruck_theme.dart';
import '../../../../shared/widgets/brand_lockup.dart';

class FlowHeader extends StatelessWidget {
  const FlowHeader({super.key, required this.stepLabel, required this.onBack});

  final String stepLabel;
  final VoidCallback onBack;

  @override
  Widget build(BuildContext context) {
    return ConstrainedBox(
      constraints: const BoxConstraints(minHeight: 56),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 4),
        child: Row(
          children: [
            _BackButton(key: const Key('onboarding-back'), onPressed: onBack),
            const Expanded(
              child: Padding(
                padding: EdgeInsets.symmetric(horizontal: 8),
                child: Center(
                  child: ExcludeSemantics(child: BrandLockup(compact: true)),
                ),
              ),
            ),
            ConstrainedBox(
              constraints: const BoxConstraints(minWidth: 44, minHeight: 44),
              child: Align(
                alignment: Alignment.centerRight,
                child: Text(
                  stepLabel,
                  style: FirstRuckTheme.caption(FirstRuckColors.muted).copyWith(
                    fontFeatures: const [FontFeature.tabularFigures()],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _BackButton extends StatefulWidget {
  const _BackButton({super.key, required this.onPressed});

  final VoidCallback onPressed;

  @override
  State<_BackButton> createState() => _BackButtonState();
}

class _BackButtonState extends State<_BackButton> {
  bool _pressed = false;

  @override
  Widget build(BuildContext context) {
    final reduceMotion = FirstRuckTheme.reduceMotion(context);

    return Semantics(
      button: true,
      label: 'Back',
      child: FocusableActionDetector(
        actions: {
          ActivateIntent: CallbackAction<ActivateIntent>(
            onInvoke: (_) {
              widget.onPressed();
              return null;
            },
          ),
        },
        child: GestureDetector(
          behavior: HitTestBehavior.opaque,
          onTap: widget.onPressed,
          onTapDown: (_) => setState(() => _pressed = true),
          onTapUp: (_) => setState(() => _pressed = false),
          onTapCancel: () => setState(() => _pressed = false),
          child: AnimatedScale(
            scale: !reduceMotion && _pressed ? 0.96 : 1,
            duration: FirstRuckTheme.motionOf(
              context,
              FirstRuckTheme.motionFast,
            ),
            child: AnimatedContainer(
              duration: FirstRuckTheme.motionOf(
                context,
                const Duration(milliseconds: 120),
              ),
              width: FirstRuckTheme.minTapTarget,
              height: FirstRuckTheme.minTapTarget,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: _pressed
                    ? FirstRuckColors.paperDeep
                    : Colors.transparent,
              ),
              alignment: Alignment.center,
              child: const ExcludeSemantics(
                child: CustomPaint(
                  size: Size(22, 22),
                  painter: _ChevronPainter(),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _ChevronPainter extends CustomPainter {
  const _ChevronPainter();

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = FirstRuckColors.ink
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.8
      ..strokeCap = StrokeCap.round
      ..strokeJoin = StrokeJoin.round;
    final path = Path()
      ..moveTo(size.width * 0.62, size.height * 0.22)
      ..lineTo(size.width * 0.36, size.height * 0.5)
      ..lineTo(size.width * 0.62, size.height * 0.78);
    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
