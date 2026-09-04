import 'package:flutter/material.dart';

import '../../../../app/first_ruck_colors.dart';
import '../../../../app/first_ruck_theme.dart';

class InlineMessage extends StatelessWidget {
  const InlineMessage({super.key, required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    if (message.isEmpty) {
      return const SizedBox(height: 22);
    }

    return Semantics(
      liveRegion: true,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const ExcludeSemantics(
            child: Padding(
              padding: EdgeInsets.only(top: 2),
              child: Icon(
                Icons.error_outline,
                size: 16,
                color: FirstRuckColors.error,
              ),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              message,
              style: FirstRuckTheme.caption(FirstRuckColors.error)
                  .copyWith(fontSize: 14),
            ),
          ),
        ],
      ),
    );
  }
}
