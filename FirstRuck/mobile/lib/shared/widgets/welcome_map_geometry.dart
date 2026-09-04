import 'dart:typed_data';

import 'package:flutter/material.dart';

/// Shared viewBox geometry for the welcome topographic art.
///
/// Coordinates match the current web SVG (`0 0 430 430`) so the native route
/// can sit in the same low-conflict upper field.
abstract final class WelcomeMapGeometry {
  static const viewBox = Size(430, 430);
  static const start = Offset(42, 348);
  static const end = Offset(391, 101);

  static Path contourA() {
    return Path()
      ..moveTo(-30, 290)
      ..cubicTo(44, 212, 91, 349, 171, 271)
      ..cubicTo(251, 193, 297, 216, 344, 123)
      ..cubicTo(391, 30, 447, 98, 471, 40);
  }

  static Path contourB() {
    return Path()
      ..moveTo(-42, 329)
      ..cubicTo(48, 248, 102, 383, 190, 305)
      ..cubicTo(278, 227, 319, 249, 366, 158)
      ..cubicTo(413, 67, 475, 131, 505, 67);
  }

  static Path contourC() {
    return Path()
      ..moveTo(-55, 369)
      ..cubicTo(52, 283, 115, 417, 210, 342)
      ..cubicTo(305, 267, 340, 283, 393, 191)
      ..cubicTo(446, 99, 498, 148, 541, 85);
  }

  static Path route() {
    return Path()
      ..moveTo(42, 348)
      ..cubicTo(92, 310, 119, 327, 149, 277)
      ..cubicTo(179, 227, 215, 221, 248, 237)
      ..cubicTo(281, 253, 302, 218, 315, 179)
      ..cubicTo(328, 140, 350, 120, 391, 101);
  }

  static Path transform(Path path, Size size) {
    final scale = size.width / viewBox.width;
    final matrix = Float64List(16);
    matrix[0] = scale;
    matrix[5] = scale;
    matrix[10] = 1;
    matrix[15] = 1;
    return path.transform(matrix);
  }

  static Offset transformOffset(Offset point, Size size) {
    final scale = size.width / viewBox.width;
    return Offset(point.dx * scale, point.dy * scale);
  }
}
